<?php
/**
 * Created by PhpStorm.
 * User: ryanr
 * Date: 12/15/2016
 * Time: 11:52 AM
 */

namespace App\Http\Controllers\Web;

use App\QuestionnaireQuestion;
use App\QuestionnaireAnswer;
use Illuminate\Http\Request;
use League\Flysystem\Exception;

class QuestionController extends AdminController
{
    protected $redirectAfterSave = 'questions';

    public function index()
    {
        $this->isAllowed('Questions.List');
        $questions = QuestionnaireQuestion::all();
        return view('questions.index', compact('questions'));
    }

    public function create()
    {
        $this->isAllowed('Questions.Create');
        return view('questions.create');
    }

    public function edit($id)
    {
        $this->isAllowed('Questions.Update');
        $question = QuestionnaireQuestion::where('id', $id)->first();
        $answers = QuestionnaireAnswer::where('question_id', $id)->get();
        return view('questions.edit', compact('question', 'answers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'description' => 'required|string',
            'type' => 'required',
            'answer.*' => 'required|string'
        ]);

        $input = $request->all();
        $input['questionnaire_question_code'] = strtolower(str_random(5));
        $question = QuestionnaireQuestion::create($input);
        foreach ($input['answer'] as $answer) {
            $inputanswer['description'] = $answer;
            $inputanswer['question_id'] = $question->id;
            QuestionnaireAnswer::create($inputanswer);
        }
        return redirect($this->redirectAfterSave)->with('success', 'Question successfully saved!');
    }

    public function update(Request $request, $id)
    {
        try {
            $question = QuestionnaireQuestion::where('id', $id)->first();
            $input = $request->all();
            if (!is_null($request->input('answer'))) {
                $in = QuestionnaireAnswer::where('question_id', $id)->whereIn('description',
                    $request->input('answer'))->pluck('description');
                $out = QuestionnaireAnswer::where('question_id', $id)->whereNotIn('description',
                    $request->input('answer'))->get();
//                dd($out);
                if (count($out) > 0) {
                    foreach ($out as $item) {
                        \DB::table('questionnaire_answers')->where('question_id',
                            $id)->where('id', $item->id)->update(array('deleted_at' => \DB::raw('NOW()')));
                    }
                }
                if (count($in) < count($request->input('answer'))) {
                    $in = array_values($in->toArray());
                    $new = array_diff(array_values($request->input('answer')), $in);
                    foreach ($new as $item){
                        $inputanswer['description'] = $item;
                        $inputanswer['question_id'] = $id;
                        QuestionnaireAnswer::create($inputanswer);
                    }
                }
            }
            $question->fill($input)->save();
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
        return redirect()->action('Web\QuestionController@index')->with('success',
            'Question successfully updated');
    }

    public function destroy($id)
    {
        try {
            QuestionnaireQuestion::where('id', $id)->delete();
            QuestionnaireAnswer::where('question_id', $id)->delete();
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
        return redirect()->action('Web\QuestionController@index')->with('success', 'Data deleted');
    }
}