<?php
namespace App\Resource;

use Doctrine\ORM\EntityRepository;
use App\Entity\Spindle;
use App\Entity\User;

/**
 * Class Resource
 */
class UserResource extends EntityRepository
{
    /**
     * Get the latest values from a spindle
     * @param  Spindle $spindle [description]
     * @return [type]           [description]
     */
    public function findByEmail($email)
    {
        try {
            return $this->findOneBy(['email' => $email]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
