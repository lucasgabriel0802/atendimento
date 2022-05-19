<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Users Model
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('T_USERWEB');
        $this->setDisplayField('USUARIOWEB');
        $this->setPrimaryKey(['UNIDADE','CODIGO']);

        $this->addBehavior('Timestamp');
    }

    public function buscarUsuario($codigo = 0, $unidade = 0){
        $query = $this->Users
        ->find();
        //->select(['codigo', 'usuarioweb'])
        //->where(['codigo =' => $codigo, 'unidade = ' => $unidade]);
        //->order(['created' => 'DESC']);
    
        return $query;
    }    

    public function validationDefault(Validator $validator)
    {
        $validator
            ->scalar('USUARIOWEB')
            ->maxLength('USUARIOWEB', 220)
            ->requirePresence('USUARIOWEB', 'create');

        $validator
            ->email('EMAIL')
            ->requirePresence('EMAIL', 'create');

        $validator
            ->scalar('USUARIOWEB')
            ->maxLength('USUARIOWEB', 220)
            ->requirePresence('USUARIOWEB', 'create');

        $validator
            ->scalar('SENHAWEB')
            ->maxLength('SENHAWEB', 220)
            ->requirePresence('SENHAWEB', 'create');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['EMAIL']));
        $rules->add($rules->isUnique(['USUARIOWEB']));

        return $rules;
    }
}
