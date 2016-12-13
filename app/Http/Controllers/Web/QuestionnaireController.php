<?php

namespace App\Http\Controllers\Web;

use App\QuestionnaireQuestion;
use App\QuestionnaireTemplate;
use App\QuestionnaireTemplateQuestions;
use App\QuestionnaireAnswer;
use Illuminate\Http\Request;

class QuestionnaireController extends AdminController
{
    protected $redirectAfterSave = 'questionnaire';

    public function index()
    {
        $questionnaire = QuestionnaireTemplate::paginate(50);
        return view('questionnaire.index', compact('questionnaire'));
    }

    public function create()
    {
        return view('questionnaire.create');
    }

    public function edit($id)
    {
        $questionnaire = $this->getQuestionnaireTemplateById($id);
        $template_question = QuestionnaireTemplateQuestions::with('question')->where('template_id', $id)->get();
        return view('questionnaire.edit', compact('questionnaire', 'template_question'));
    }

    public function store(Request $request)
    {
        $request->session()->flash('countExistingQuestion', $this->countExistingQuestion($request));
        $this->validate($request, [
            'start_at' => '',
            'end_at' => '',
            'created_by' => '',
            'total_point' => '',
            'description' => '',
            'question.description.*' => '',
            'question.type.*' => '',
            'question.*.answer.*' => ''
        ]);

        try {
            \DB::beginTransaction();
            $template = $this->createNewTemplate($request);
            $question = $this->createNewQuestion($request);
            $answer = $this->createNewAnswer($request);
            $this->persistData($template, $question);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            logger($e);
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Questionnaire successfully saved!');
    }

    public function update(Request $request, $id)
    {
    }

    public function show($id)
    {
    }

    public function destroy($id)
    {
    }

    private function createNewQuestion($request)
    {
    }

    private function createNewAnswer($request, $question_id)
    {
    }

    private function createNewTemplate($request, $id = null)
    {
        $code = strtolower(str_random(5));


        $memberCode = strtolower(str_random(10));

        $m = is_null($id) ? new Merchant : $this->getMerchantById($id);
        $m->merchant_code = $memberCode;
        $m->company_name = $request->input('company_name');
        $m->address = $request->input('address');
        $m->company_email = $request->input('company_email');

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $filename = sprintf(
                "%s-%s.%s",
                $memberCode,
                date('Ymdhis')
            );

            $m->company_logo = $filename;
        }

        $m->save();

        return $m;
    }

    private function persistData($template, $question)
    {
        foreach ($question as $item) {
            $tq = new \App\QuestionnaireTemplateQuestions();
            $tq->template()->associate($template);
            $tq->question()->associate($question);
            $tq->save();
        }

        return true;
    }

    private function countExistingQuestion($request)
    {
        $count = count($request->input('question')['description']);
        return 0 === $count ? 0 : $count - 1;
    }

    private function countNewQuestion($request)
    {
        $count = count($request->input('newquestion')['description']);
        return 0 === $count ? 0 : $count - 1;
    }

    private function getQuestionnaireTemplateById($id)
    {
        return QuestionnaireTemplate::where('id', $id)->first();
    }

    private function getQuestionById($id)
    {
        return QuestionnaireQuestion::where('id', $id)->first();
    }

    private function getAnswerById($id)
    {
        return QuestionnaireAnswer::where('id', $id)->first();
    }

    private function getQuestionAnswer($id)
    {
//        $question = QuestionnaireQuestion::where('id', $id)->first();
        $answer = QuestionnaireAnswer::where('question_id', $id)->get();
        return compact('question', 'answer');
    }
}