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
        return view('questionnaire.question_index', compact('questions'));
    }

    public function create()
    {
        $this->isAllowed('Questions.Create');
        return view('questionnaire.question_create');
    }

    public function edit($id)
    {
        $this->isAllowed('Questions.Update');
        $question = QuestionnaireQuestion::where('id', $id)->first();
        $answers = QuestionnaireAnswer::where('question_id', $id)->get();
        return view('questionnaire.question_edit', compact('question', 'answers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'description' => 'required|string',
            'type' => 'required',
            'answer.*' => 'string'
        ]);

        $input = $request->all();
        $input['questionnaire_question_code'] = strtolower(str_random(5));
        $question = QuestionnaireQuestion::create($input);
        if ($input['type'] != 'input') {
            foreach ($input['answer'] as $answer) {
                $inputanswer['description'] = $answer;
                $inputanswer['question_id'] = $question->id;
                QuestionnaireAnswer::create($inputanswer);
            }
        }
        if ($input['_from'] == 'question') {
            return redirect($this->redirectAfterSave)->with('success', 'Question successfully saved!');
        } elseif ($input['_from'] == 'questionnaire') {
            return $question;
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required|string',
            'type' => 'required',
            'answer.*' => 'string'
        ]);

        try {
            $question = QuestionnaireQuestion::where('id', $id)->first();
            $input = $request->all();
            if (!is_null($request->input('answer')) && $request->input('type') != 'input') {
                $in = QuestionnaireAnswer::where('question_id', $id)->whereIn('description',
                    $request->input('answer'))->pluck('description');
                $out = QuestionnaireAnswer::where('question_id', $id)->whereNotIn('description',
                    $request->input('answer'))->get();
                if (count($out) > 0) {
                    foreach ($out as $item) {
                        \DB::table('questionnaire_answers')->where('question_id',
                            $id)->where('id', $item->id)->update(array('deleted_at' => \DB::raw('NOW()')));
                    }
                }
                if (count($in) < count($request->input('answer'))) {
                    $in = array_values($in->toArray());
                    $new = array_diff(array_values($request->input('answer')), $in);
                    foreach ($new as $item) {
                        $inputanswer['description'] = $item;
                        $inputanswer['question_id'] = $id;
                        QuestionnaireAnswer::create($inputanswer);
                    }
                }
            } else {
                $out = QuestionnaireAnswer::where('question_id', $id)->get();
                if (count($out) > 0) {
                    foreach ($out as $item) {
                        \DB::table('questionnaire_answers')->where('question_id',
                            $id)->where('id', $item->id)->update(array('deleted_at' => \DB::raw('NOW()')));
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