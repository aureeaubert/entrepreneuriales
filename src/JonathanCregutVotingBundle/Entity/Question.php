<?php

namespace JonathanCregutVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="JonathanCregutVotingBundle\Entity\QuestionRepository")
 */
class Question
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
     * @ORM\Column(name="content", type="string", length=1000)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;


    public function __construct()
    {
        $this->state = 0;
    }



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
     * Set content
     *
     * @param string $content
     * @return Question
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Question
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }
    
     /**
     * @ORM\ManyToMany(targetEntity="JonathanCregutVotingBundle\Entity\Participant")
     */
    private $participants;
    
    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Question
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getParticipants()
    {
        return $this->participants;
    }
    
}
