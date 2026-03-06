<?php

// Functions and constants

namespace {

}


namespace LearnDash\Groups_Plus {

    class AliasAutoloader
    {
        private string $includeFilePath;

        private array $autoloadAliases = array (
  'WP_Async_Request' => 
  array (
    'type' => 'class',
    'classname' => 'WP_Async_Request',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Groups_Plus_WP_Async_Request',
    'implements' => 
    array (
    ),
  ),
  'WP_Background_Process' => 
  array (
    'type' => 'class',
    'classname' => 'WP_Background_Process',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Groups_Plus_WP_Background_Process',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\App' => 
  array (
    'type' => 'class',
    'classname' => 'App',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\App',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\CallableBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'CallableBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Builders\\CallableBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
      1 => 'lucatume\\DI52\\Builders\\ReinitializableBuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\ClassBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ClassBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Builders\\ClassBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
      1 => 'lucatume\\DI52\\Builders\\ReinitializableBuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\ClosureBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ClosureBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Builders\\ClosureBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\Factory' => 
  array (
    'type' => 'class',
    'classname' => 'Factory',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Builders\\Factory',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\Parameter' => 
  array (
    'type' => 'class',
    'classname' => 'Parameter',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Builders\\Parameter',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\Resolver' => 
  array (
    'type' => 'class',
    'classname' => 'Resolver',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Builders\\Resolver',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\ValueBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ValueBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Builders\\ValueBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Container' => 
  array (
    'type' => 'class',
    'classname' => 'Container',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Container',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'Psr\\Container\\ContainerInterface',
    ),
  ),
  'lucatume\\DI52\\ContainerException' => 
  array (
    'type' => 'class',
    'classname' => 'ContainerException',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\ContainerException',
    'implements' => 
    array (
      0 => 'Psr\\Container\\ContainerExceptionInterface',
    ),
  ),
  'lucatume\\DI52\\NestedParseError' => 
  array (
    'type' => 'class',
    'classname' => 'NestedParseError',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\NestedParseError',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\NotFoundException' => 
  array (
    'type' => 'class',
    'classname' => 'NotFoundException',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\NotFoundException',
    'implements' => 
    array (
      0 => 'Psr\\Container\\NotFoundExceptionInterface',
    ),
  ),
  'lucatume\\DI52\\ServiceProvider' => 
  array (
    'type' => 'class',
    'classname' => 'ServiceProvider',
    'isabstract' => true,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\ServiceProvider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\Actions\\DisplayNoticesInAdmin' => 
  array (
    'type' => 'class',
    'classname' => 'DisplayNoticesInAdmin',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\Actions',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\Actions\\DisplayNoticesInAdmin',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\Actions\\EnqueueNoticesScriptsAndStyles' => 
  array (
    'type' => 'class',
    'classname' => 'EnqueueNoticesScriptsAndStyles',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\Actions',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\Actions\\EnqueueNoticesScriptsAndStyles',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\Actions\\NoticeShouldRender' => 
  array (
    'type' => 'class',
    'classname' => 'NoticeShouldRender',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\Actions',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\Actions\\NoticeShouldRender',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\Actions\\RenderAdminNotice' => 
  array (
    'type' => 'class',
    'classname' => 'RenderAdminNotice',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\Actions',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\Actions\\RenderAdminNotice',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\AdminNotice' => 
  array (
    'type' => 'class',
    'classname' => 'AdminNotice',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\AdminNotice',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\AdminNotices' => 
  array (
    'type' => 'class',
    'classname' => 'AdminNotices',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\AdminNotices',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\DataTransferObjects\\NoticeElementProperties' => 
  array (
    'type' => 'class',
    'classname' => 'NoticeElementProperties',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\DataTransferObjects',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\DataTransferObjects\\NoticeElementProperties',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\Exceptions\\NotificationCollisionException' => 
  array (
    'type' => 'class',
    'classname' => 'NotificationCollisionException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\Exceptions',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\Exceptions\\NotificationCollisionException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\NotificationsRegistrar' => 
  array (
    'type' => 'class',
    'classname' => 'NotificationsRegistrar',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\NotificationsRegistrar',
    'implements' => 
    array (
      0 => 'StellarWP\\AdminNotices\\Contracts\\NotificationsRegistrarInterface',
    ),
  ),
  'StellarWP\\AdminNotices\\ValueObjects\\NoticeLocation' => 
  array (
    'type' => 'class',
    'classname' => 'NoticeLocation',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\ValueObjects',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\ValueObjects\\NoticeLocation',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\ValueObjects\\NoticeUrgency' => 
  array (
    'type' => 'class',
    'classname' => 'NoticeUrgency',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\ValueObjects',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\ValueObjects\\NoticeUrgency',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\ValueObjects\\ScreenCondition' => 
  array (
    'type' => 'class',
    'classname' => 'ScreenCondition',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\ValueObjects',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\ValueObjects\\ScreenCondition',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\ValueObjects\\Script' => 
  array (
    'type' => 'class',
    'classname' => 'Script',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\ValueObjects',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\ValueObjects\\Script',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\ValueObjects\\Style' => 
  array (
    'type' => 'class',
    'classname' => 'Style',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\ValueObjects',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\ValueObjects\\Style',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\ValueObjects\\UserCapability' => 
  array (
    'type' => 'class',
    'classname' => 'UserCapability',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\ValueObjects',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\ValueObjects\\UserCapability',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Arrays\\Arr' => 
  array (
    'type' => 'class',
    'classname' => 'Arr',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Arrays',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\Arrays\\Arr',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\SuperGlobals\\SuperGlobals' => 
  array (
    'type' => 'class',
    'classname' => 'SuperGlobals',
    'isabstract' => false,
    'namespace' => 'StellarWP\\SuperGlobals',
    'extends' => 'LearnDash\\Groups_Plus\\StellarWP\\SuperGlobals\\SuperGlobals',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\Traits\\HasNamespace' => 
  array (
    'type' => 'trait',
    'traitname' => 'HasNamespace',
    'namespace' => 'StellarWP\\AdminNotices\\Traits',
    'use' => 
    array (
      0 => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\Traits\\HasNamespace',
    ),
  ),
  'lucatume\\DI52\\Builders\\BuilderInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'BuilderInterface',
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 
    array (
      0 => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Builders\\BuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\ReinitializableBuilderInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ReinitializableBuilderInterface',
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 
    array (
      0 => 'LearnDash\\Groups_Plus\\lucatume\\DI52\\Builders\\ReinitializableBuilderInterface',
    ),
  ),
  'Psr\\Container\\ContainerExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ContainerExceptionInterface',
    'namespace' => 'Psr\\Container',
    'extends' => 
    array (
      0 => 'LearnDash\\Groups_Plus\\Psr\\Container\\ContainerExceptionInterface',
    ),
  ),
  'Psr\\Container\\ContainerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ContainerInterface',
    'namespace' => 'Psr\\Container',
    'extends' => 
    array (
      0 => 'LearnDash\\Groups_Plus\\Psr\\Container\\ContainerInterface',
    ),
  ),
  'Psr\\Container\\NotFoundExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'NotFoundExceptionInterface',
    'namespace' => 'Psr\\Container',
    'extends' => 
    array (
      0 => 'LearnDash\\Groups_Plus\\Psr\\Container\\NotFoundExceptionInterface',
    ),
  ),
  'StellarWP\\AdminNotices\\Contracts\\NotificationsRegistrarInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'NotificationsRegistrarInterface',
    'namespace' => 'StellarWP\\AdminNotices\\Contracts',
    'extends' => 
    array (
      0 => 'LearnDash\\Groups_Plus\\StellarWP\\AdminNotices\\Contracts\\NotificationsRegistrarInterface',
    ),
  ),
);

        public function __construct()
        {
            $this->includeFilePath = __DIR__ . '/autoload_alias.php';
        }

        public function autoload($class)
        {
            if (!isset($this->autoloadAliases[$class])) {
                return;
            }
            switch ($this->autoloadAliases[$class]['type']) {
                case 'class':
                        $this->load(
                            $this->classTemplate(
                                $this->autoloadAliases[$class]
                            )
                        );
                    break;
                case 'interface':
                    $this->load(
                        $this->interfaceTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                case 'trait':
                    $this->load(
                        $this->traitTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                default:
                    // Never.
                    break;
            }
        }

        private function load(string $includeFile)
        {
            file_put_contents($this->includeFilePath, $includeFile);
            include $this->includeFilePath;
            file_exists($this->includeFilePath) && unlink($this->includeFilePath);
        }

        private function classTemplate(array $class): string
        {
            $abstract = $class['isabstract'] ? 'abstract ' : '';
            $classname = $class['classname'];
            if (isset($class['namespace'])) {
                $namespace = "namespace {$class['namespace']};";
                $extends = '\\' . $class['extends'];
                $implements = empty($class['implements']) ? ''
                : ' implements \\' . implode(', \\', $class['implements']);
            } else {
                $namespace = '';
                $extends = $class['extends'];
                $implements = !empty($class['implements']) ? ''
                : ' implements ' . implode(', ', $class['implements']);
            }
            return <<<EOD
                <?php
                $namespace
                $abstract class $classname extends $extends $implements {}
                EOD;
        }

        private function interfaceTemplate(array $interface): string
        {
            $interfacename = $interface['interfacename'];
            $namespace = isset($interface['namespace'])
            ? "namespace {$interface['namespace']};" : '';
            $extends = isset($interface['namespace'])
            ? '\\' . implode('\\ ,', $interface['extends'])
            : implode(', ', $interface['extends']);
            return <<<EOD
                <?php
                $namespace
                interface $interfacename extends $extends {}
                EOD;
        }
        private function traitTemplate(array $trait): string
        {
            $traitname = $trait['traitname'];
            $namespace = isset($trait['namespace'])
            ? "namespace {$trait['namespace']};" : '';
            $uses = isset($trait['namespace'])
            ? '\\' . implode(';' . PHP_EOL . '    use \\', $trait['use'])
            : implode(';' . PHP_EOL . '    use ', $trait['use']);
            return <<<EOD
                <?php
                $namespace
                trait $traitname { 
                    use $uses; 
                }
                EOD;
        }
    }

    spl_autoload_register([ new AliasAutoloader(), 'autoload' ]);
}
