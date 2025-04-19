<?php
namespace App\Model;

class Room
{
    // Essential attributes
    private $id;
    private $name;
    private $description;
    private $familyId; //family id is the user id that created the room
    private $dateCreated;
    private $code;


    // Constructor
    public function __construct($id, $name, $description, $familyId, $dateCreated, $code = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->familyId = $familyId;
        $this->dateCreated = $dateCreated;
        $this->code = $code;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }


    public function getFamilyId()
    {
        return $this->familyId;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getCode()
    {
        return $this->code;
    }


    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setFamilyId($familyId)
    {
        $this->familyId = $familyId;
    }
    public function setDateCreated($dateCreated){
        $this->dateCreated = $dateCreated;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function generateRoomCode($length = 6): string {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Exclude ambiguous chars
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'family_id' => $this->familyId,
            'date_created' => $this->dateCreated,
            'code' => $this->code
        ];
    }
}

