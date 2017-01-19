<?php

namespace App\Http\Controllers\Web;

use App\QuestionnaireQuestion;
use App\QuestionnaireTemplate;
use Illuminate\Http\Request;
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
}