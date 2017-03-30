<?php

namespace JonathanCregutVotingBundle\Controller;

use JonathanCregutVotingBundle\Entity\Participant;
use JonathanCregutVotingBundle\Entity\Question;
use JonathanCregutVotingBundle\Entity\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Resp;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('JonathanCregutVotingBundle:Default:home.html.twig', array('title' => "Accueil du vote par sms", 'subtitle' => "Voulez vous créer une nouvelle question ou voir des réponses"));
    }
    
    public function questionsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        
        $question = new Question();

        
        $form = $this->createFormBuilder($question)
            ->add('content', 'text')
            ->add('participants', 'entity', array(
                'class' => 'JonathanCregutVotingBundle:Participant',
                'property'     => 'name',
                'multiple'     => true
            ))
            ->add('save', 'submit', array(
                'attr' => array(
                    'class' => 'btn btn-success'
                )
            ))
            ->getForm();
            
            $form->handleRequest($request);

        if($form->isValid()) {
            $em->persist($question);
            $em->flush();
            return $this->redirect($this->generateUrl("questions"));
        }
        
        return $this->render('JonathanCregutVotingBundle:Default:questions.html.twig', array('title' => "Ajout d'une nouvelle question par SMS",
        'subtitle' => "Ajouter une nouvelle question cloturera la précédente",
        'form' => $form->createView()));
    }
    
    public function responsesAction(){
        $em = $this->getDoctrine()->getManager();
        $questionsRepo = $em->getRepository('JonathanCregutVotingBundle:Question');
        $questions = $questionsRepo->findBy(
            array(),
            array('id' => 'desc'),
            null,
            null);
        
        return $this->render('JonathanCregutVotingBundle:Default:responses.html.twig', array('title' => "Consultation des réponses",
        'subtitle' => "Cliquez sur une question pour voir ses résultats",
        'questions' => $questions));
    }
    
    public function detailedResultsAction($question_id){
        $em = $this->getDoctrine()->getManager();
        
        $questionsRepo = $em->getRepository('JonathanCregutVotingBundle:Question');
        $question = $questionsRepo->find($question_id);
        
        $responsesRepo = $em->getRepository('JonathanCregutVotingBundle:Response');
        $responses = $responsesRepo->findBy(
            array('question' => $question),
            array('id' => 'desc'),
            null,
            null);
            
        //$participantsRepo = $em->getRepository('JonathanCregutVotingBundle:Participant');
        //$participantsList = $participantsRepo->findAll();
        $participantsList = $question->getParticipants();
        
        $total = 0;
        
        foreach($participantsList as $participant){
            $currentResult = $responsesRepo->findBy(array('question' => $question, 'choice' => $participant),
            array('id' => 'desc'),
            null,
            null);
            
            $participant->setVotes(count($currentResult));
            $total += $participant->getVotes();
        }
        
        if($total == 0){
            $total = 1;
            $subtitle = "Votes : 0";
        }else{
            $subtitle = "Votes : " . $total;
        }
        
        return $this->render('JonathanCregutVotingBundle:Default:display_details.html.twig', array('title' => "Votez au 06.45.77.01.65 : ".$question->getContent(),
        'subtitle' => $subtitle,
        'participants' => $participantsList,
        'total' => $total));
    }
    
    public function displayAction(){
        return $this->render('JonathanCregutVotingBundle:Default:display.html.twig', array('title' => "Question en cours",
        'subtitle' => "Affichage des réponses en direct"));
    }
    
    public function participantsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        
        $participant = new Participant();
        
        $form = $this->createFormBuilder($participant)
            ->add('name', 'text')
            ->add('number', 'number')
            ->add('save', 'submit', array(
                'attr' => array(
                    'class' => 'btn btn-success'
                )
            ))
            ->getForm();
            
            $form->handleRequest($request);

        if($form->isValid()){
            $em->persist($participant);
            $em->flush();

            return $this->redirect($this->generateUrl("participants"));
        }
        
        $participantsRepo = $em->getRepository('JonathanCregutVotingBundle:Participant');
        $participantsList = $participantsRepo->findBy(
            array(),
            array('number' => 'asc'),
            null,
            null
        );
        
        
        return $this->render('JonathanCregutVotingBundle:Default:participants.html.twig', array('title' => "Participants",
        'subtitle' => "Liste et ajout de participants",
        'participants' => $participantsList,
        'form' => $form->createView()));
    }
    
    public function voteAction(Request $request) {
        $value = $request->query->get('text', 'null');
        $pn = $request->query->get('phone', 'null');
        
        if($value != 'null' && $pn != 'null') {

            $em = $this->getDoctrine()->getManager();
            $participantRepo = $em->getRepository('JonathanCregutVotingBundle:Participant');
            $participantsList = $participantRepo->findBy(
            array('number'=>$value),
            array(),
            null,
            null
            );
            if($participantsList == null) {
                return new Resp('error');
            } else {
                $questionsRepo = $em->getRepository('JonathanCregutVotingBundle:Question');
                $currentQuestion = $questionsRepo->findBy(
                    array('state' => 1),
                    array(),
                    null,
                    null
                );
                
                $responsesRepo = $em->getRepository('JonathanCregutVotingBundle:Response');
                $currentResponse = $responsesRepo->findBy(
                    array('phoneNumber' => $pn, 'question' => $currentQuestion[0]),
                    array(),
                    null,
                    null);
                    
                if($currentResponse == null){
                    $response = new Response();
                    $response->setPhoneNumber($pn);
                    $response->setChoice($participantsList[0]);
                    $response->setQuestion($currentQuestion[0]);
                    $em->persist($response);
                    $em->flush();
                }else{
                    $response = $currentResponse[0];
                    $response->setChoice($participantsList[0]);
                    $em->persist($response);
                    $em->flush();
                }
                
                return new Resp('ok');
            }
        }else{
            return new Resp('missing arg');
        }
    }
    
    public function stateQuestionAction($question_id, $state) {
        $em = $this->getDoctrine()->getManager();
        $questionsRepo = $em->getRepository('JonathanCregutVotingBundle:Question');
        
        $question = $questionsRepo->find($question_id);
        
        if(($question->getState() == 0 && $state == 1) || ($question->getState() == 1 && $state == 2)) {
                        
            foreach($questionsRepo->findAll() as $q) {
                if($q->getState() == 1) {
                    $q->setState(2);
                    $em->persist($q);
                }
            }

            $question->setState($state);

            $em->persist($question);
            $em->flush();
            
        }
        
        
        
        return $this->redirect($this->generateUrl('responses'));
    }
}
