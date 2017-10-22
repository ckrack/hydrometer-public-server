<?php
namespace App\Resource;

use Doctrine\ORM\EntityRepository;
use App\Entity\Hydrometer;
use App\Entity\User;

/**
 * Class Resource
 */
class TokenResource extends EntityRepository
{
    /**
     * Get the latest values from a hydrometer
     * @param  Hydrometer $hydrometer [description]
     * @return [type]           [description]
     */
    public function findByValue($token)
    {
        try {
            return $this->findOneBy(['value' => $token]);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }
}
