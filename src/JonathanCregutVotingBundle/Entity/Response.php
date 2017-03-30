<?php

namespace JonathanCregutVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Response
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="JonathanCregutVotingBundle\Entity\ResponseRepository")
 */
class Response
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=255)
     */
    private $phoneNumber;

    /**
    * @ORM\ManyToOne(targetEntity="JonathanCregutVotingBundle\Entity\Participant")
    * @ORM\JoinColumn(nullable=false)
    */
    private $choice;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return Response
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set choice
     *
     * @param string $choice
     * @return Response
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;

        return $this;
    }

    /**
     * Get choice
     *
     * @return  
     */
    public function getChoice()
    {
        return $this->choice;
    }
    
    /**
    * @ORM\ManyToOne(targetEntity="JonathanCregutVotingBundle\Entity\Question")
    * @ORM\JoinColumn(nullable=false)
    */
    private $question;
    
    
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get choice
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question;
    }
    

}
