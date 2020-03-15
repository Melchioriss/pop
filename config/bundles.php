<?php

return [
    // tests will probably fall with
    // "RuntimeException: Initialization of AOP loader was failed, probably due to Debug::enable()"
    // investigaions are required
    // Go\Symfony\GoAopBundle\GoAopBundle::class => ['all' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    League\Tactician\Bundle\TacticianBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    DAMA\DoctrineTestBundle\DAMADoctrineTestBundle::class => ['test' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Nelmio\ApiDocBundle\NelmioApiDocBundle::class => ['all' => true],
    Knojector\SteamAuthenticationBundle\KnojectorSteamAuthenticationBundle::class => ['all' => true],
    AutoMapperPlus\AutoMapperPlusBundle\AutoMapperPlusBundle::class => ['all' => true],
    Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle::class => ['dev' => true, 'test' => true],
];
