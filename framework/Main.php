<?php

namespace Framework;

use Framework\Components\ResolverComponent;
use Symfony\Component\Yaml\Yaml;
use ReflectionClass;
use Framework\Entities\User;
use Framework\Database\DB;

class Main
{

    public static function run(): void
    {
        $YML = Yaml::parseFile(__DIR__.'/../config/pages/page.example.yml');
        foreach ($YML['register']['type'] as $value) {
            ResolverComponent::resolve($value);
        }
        try {
            $user = new User(169, 'Cezar Teste up', 'cezar.teste@gmail.com', '123456', date('Y-m-d H:i:s'));
            $user->update();
            var_dump($user->findAll());
            var_dump(DB::execute('select * from users where id = ?', [5]));

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

}
