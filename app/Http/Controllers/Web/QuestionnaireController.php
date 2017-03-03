<?php

namespace App\Http\Controllers\Web;

use App\QuestionnaireQuestion;
use App\QuestionnaireSubmit;
use App\QuestionnaireTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class QuestionnaireController extends AdminController
{
    protected $redirectAfterSave = 'questionnaire';

    public function index()
    {
        $this->isAllowed('Questionnaire.List');
        if ($this->isSuperAdministrator()) {
            $questionnaire = QuestionnaireTemplate::orderBy('status', 'desc')->orderBy('start_at', 'asc')->get();
        } else {
            $questionnaire = QuestionnaireTemplate::where('created_by', auth()->user()->id)->orderBy('status',
                'desc')->orderBy('start_at', 'asc')->get();
        }
        return view('questionnaire.questionnaire_index', compact('questionnaire'));
    }

    public function create()
    {
        $this->isAllowed('Questionnaire.Create');
        $questions = QuestionnaireQuestion::all();
        return view('questionnaire.questionnaire_create', compact('questions'));
    }

    public function edit($id)
    {
        $this->isAllowed('Questionnaire.Update');
        $questionnaire = $this->getQuestionnaireTemplateById($id);
        $questions = QuestionnaireQuestion::all();
        return view('questionnaire.questionnaire_edit', compact('questionnaire', 'questions'));
    }

    public function store(Request $request)
    {
        $this->isAllowed('Questionnaire.Create');
        $period = explode(' - ', $request->input('period'));
        $this->validate($request, [
            'period' => 'required',
            'description' => 'required',
            'question.*' => 'required'
        ]);

        $input = $request->all();

        if (!auth()->user()->hasRole('Questionnaire.Point') || !$request->input('total_point') || $request->input('total_point') == 0) {
            $input['status'] = 'new';
            $input['total_point'] = 0;
        }

        if (!auth()->user()->hasRole('Questionnaire.Publish')) {
            $input['status'] = 'new';
        }

        $input['questionnaire_template_code'] = strtolower(str_random(5));
        $input['start_at'] = $period[0];
        $input['end_at'] = $period[1];
        $input['created_by'] = auth()->user()->id;
        $questionnaire = QuestionnaireTemplate::create($input);
        $questionnaire->questions()->sync($request->input('question'));
        return redirect($this->redirectAfterSave)->with('success', 'Questionnaire successfully saved!');
    }

    public function update(Request $request, $id)
    {
        $this->isAllowed('Questionnaire.Update');
        try {

            $questionnaire = QuestionnaireTemplate::where('id', $id)->first();
            $input = $request->all();

            if (!is_null($request->input('period'))) {
                $period = explode(' - ', $request->input('period'));
                $input['start_at'] = $period[0];
                $input['end_at'] = $period[1];
            }

            if (!is_null($request->input('question'))) {
                $questionnaire->questions()->sync($request->input('question'));
            }

            if ($input['total_point'] == 0) {
                $input['status'] = 'new';
            }

            $questionnaire->fill($input)->save();

        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
        return redirect()->action('Web\QuestionnaireController@index')->with('success',
            'Questionnaire successfully updated');
    }

    public function show($id)
    {

    }

    public function destroy($id)
    {
        $this->isAllowed('Questionnaire.Delete');
        try {
            QuestionnaireTemplate::where('id', $id)->delete();
            \DB::table('questionnaire_templates_questions')->where('template_id',
                $id)->update(array('deleted_at' => \DB::raw('NOW()')));
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
        return redirect()->action('Web\QuestionnaireController@index')->with('success', 'Data deleted');
    }

    private function getQuestionnaireTemplateById($id)
    {
        return QuestionnaireTemplate::where('id', $id)->first();
    }

    public function publish($id)
    {
        $this->isAllowed('Questionnaire.Publish');
        $questionnaire = $this->getQuestionnaireTemplateById($id);
        if ($questionnaire['total_point'] > 0) {
            $input['status'] = 'publish';
            $questionnaire->fill($input)->save();
            return redirect($this->redirectAfterSave)->with('success', 'Questionnaire published');
        } else {
            return back()->with('errors', 'Total points must be greater than 0');
        }
    }

    public function resultShow($id)
    {
        $this->isAllowed('Questionnaire.List');

        $results = DB::table('questionnaire_submits as qs')
            ->join('questionnaire_submit_questions as qsq', 'qs.id', '=', 'qsq.submit_id')
            ->join('questionnaire_submit_answers as qsa', 'qsq.id', '=', 'qsa.question_id')
            ->join('questionnaire_templates as qt', 'qs.template_id', '=', 'qt.id')
            ->join('members as m', 'qs.member_id', '=', 'm.id')
            ->where('qs.id', $id)
            ->select('qs.template_id as template_id', 'qs.created_at as created_at', 'qs.total_point as total_point',
                'qsq.id as question_id', 'qsq.question as question', 'qsq.type as question_type', 'qsa.id as answer_id',
                'qsa.answer as answer', 'qt.description as description', 'm.name as member')
            ->get()
            ->groupBy('question_id')
            ->values()
            ->all();

        $details = new \stdClass;
        $details->description = $results[0][0]->description;
        $details->template_id = $results[0][0]->template_id;
        $details->total_point = $results[0][0]->total_point;
        $details->created_at = $results[0][0]->created_at;
        $details->member = $results[0][0]->member;

        $questions = [];
        foreach ($results as $result) {
            $q = new \stdClass;
            $q->question = $result[0]->question;
            $q->question_type = $result[0]->question_type;

            $answers = [];
            foreach ($result as $answer) {
                $answers[] = $answer->answer;
            }

            $q->answers = $answers;

            $questions[] = $q;
        }

        $details->questions = $questions;

        return view('questionnaire.result_show', compact('details'));
    }

    public function resultList($id)
    {
        $this->isAllowed('Questionnaire.List');
        $results = QuestionnaireSubmit::with('member', 'template')->where('template_id', $id)->get();
        return view('questionnaire.result_index', compact('results'));
    }
}