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
        $questionnaire = QuestionnaireTemplate::all();
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
        $period = explode(' - ', $request->input('period'));
        $this->validate($request, [
            'period' => 'required',
            'total_point' => 'required',
            'description' => 'required',
            'question.*' => 'required'
        ]);

        $input = $request->all();
        $input['questionnaire_template_code'] = strtolower(str_random(5));
        $input['start_at'] = $period[0];
        $input['end_at'] = $period[1];
        $input['created_by'] = auth()->user()->name;
        $questionnaire = QuestionnaireTemplate::create($input);
        $questionnaire->questions()->attach($request->input('question'));
        return redirect($this->redirectAfterSave)->with('success', 'Questionnaire successfully saved!');
    }

    public function update(Request $request, $id)
    {
        try {
            $questionnaire = QuestionnaireTemplate::where('id', $id)->first();
            $input = $request->all();
            $period = explode(' - ', $request->input('period'));
            $input['start_at'] = $period[0];
            $input['end_at'] = $period[1];
            if (!is_null($request->input('question'))) {
                $questionnaire->questions()->sync($request->input('question'));
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
}