<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;

class Events
{
    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private ?string $summary;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private ?string $description;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private ?string $start;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private ?string $end;

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param string|null $summary
     * @return Events
     */
    public function setSummary(?string $summary): Events
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Events
     */
    public function setDescription(?string $description): Events
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStart(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->start);
    }

    /**
     * @param string|null $start
     * @return Events
     */
    public function setStart(?string $start): Events
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getEnd(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->end);
    }

    /**
     * @param string|null $end
     * @return Events
     */
    public function setEnd(?string $end): Events
    {
        $this->end = $end;
        return $this;
    }
}
