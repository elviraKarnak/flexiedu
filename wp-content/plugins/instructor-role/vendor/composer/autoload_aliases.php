<?php



namespace LearnDash\Instructor_Role {

    class AliasAutoloader
    {
        private string $includeFilePath;

        private array $autoloadAliases = array (
  'StellarWP\\Arrays\\Arr' => 
  array (
    'type' => 'class',
    'classname' => 'Arr',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Arrays',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\Arrays\\Arr',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Asset' => 
  array (
    'type' => 'class',
    'classname' => 'Asset',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\Assets\\Asset',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Assets' => 
  array (
    'type' => 'class',
    'classname' => 'Assets',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\Assets\\Assets',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\Assets\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Controller' => 
  array (
    'type' => 'class',
    'classname' => 'Controller',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\Assets\\Controller',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Data' => 
  array (
    'type' => 'class',
    'classname' => 'Data',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\Assets\\Data',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Utils' => 
  array (
    'type' => 'class',
    'classname' => 'Utils',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\Assets\\Utils',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\DB' => 
  array (
    'type' => 'class',
    'classname' => 'DB',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\DB',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\Database\\Actions\\EnableBigSqlSelects' => 
  array (
    'type' => 'class',
    'classname' => 'EnableBigSqlSelects',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\Database\\Actions',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\Database\\Actions\\EnableBigSqlSelects',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\Database\\Exceptions\\DatabaseQueryException' => 
  array (
    'type' => 'class',
    'classname' => 'DatabaseQueryException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\Database\\Exceptions',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\Database\\Exceptions\\DatabaseQueryException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\Database\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\Database',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\Database\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\From' => 
  array (
    'type' => 'class',
    'classname' => 'From',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\From',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Having' => 
  array (
    'type' => 'class',
    'classname' => 'Having',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\Having',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Join' => 
  array (
    'type' => 'class',
    'classname' => 'Join',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\Join',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\JoinCondition' => 
  array (
    'type' => 'class',
    'classname' => 'JoinCondition',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\JoinCondition',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\MetaTable' => 
  array (
    'type' => 'class',
    'classname' => 'MetaTable',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\MetaTable',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\OrderBy' => 
  array (
    'type' => 'class',
    'classname' => 'OrderBy',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\OrderBy',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\RawSQL' => 
  array (
    'type' => 'class',
    'classname' => 'RawSQL',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\RawSQL',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Select' => 
  array (
    'type' => 'class',
    'classname' => 'Select',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\Select',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Union' => 
  array (
    'type' => 'class',
    'classname' => 'Union',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\Union',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Where' => 
  array (
    'type' => 'class',
    'classname' => 'Where',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Clauses\\Where',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\JoinQueryBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'JoinQueryBuilder',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\JoinQueryBuilder',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\QueryBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'QueryBuilder',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\QueryBuilder',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Types\\JoinType' => 
  array (
    'type' => 'class',
    'classname' => 'JoinType',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Types',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Types\\JoinType',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Types\\Math' => 
  array (
    'type' => 'class',
    'classname' => 'Math',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Types',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Types\\Math',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Types\\Operator' => 
  array (
    'type' => 'class',
    'classname' => 'Operator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Types',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Types\\Operator',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Types\\Type' => 
  array (
    'type' => 'class',
    'classname' => 'Type',
    'isabstract' => true,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Types',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Types\\Type',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\WhereQueryBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'WhereQueryBuilder',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder',
    'extends' => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\WhereQueryBuilder',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\Aggregate' => 
  array (
    'type' => 'trait',
    'traitname' => 'Aggregate',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\Aggregate',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\CRUD' => 
  array (
    'type' => 'trait',
    'traitname' => 'CRUD',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\CRUD',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\FromClause' => 
  array (
    'type' => 'trait',
    'traitname' => 'FromClause',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\FromClause',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\GroupByStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'GroupByStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\GroupByStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\HavingClause' => 
  array (
    'type' => 'trait',
    'traitname' => 'HavingClause',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\HavingClause',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\JoinClause' => 
  array (
    'type' => 'trait',
    'traitname' => 'JoinClause',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\JoinClause',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\LimitStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'LimitStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\LimitStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\MetaQuery' => 
  array (
    'type' => 'trait',
    'traitname' => 'MetaQuery',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\MetaQuery',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\OffsetStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'OffsetStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\OffsetStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\OrderByStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'OrderByStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\OrderByStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\SelectStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'SelectStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\SelectStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\TablePrefix' => 
  array (
    'type' => 'trait',
    'traitname' => 'TablePrefix',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\TablePrefix',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\UnionOperator' => 
  array (
    'type' => 'trait',
    'traitname' => 'UnionOperator',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\UnionOperator',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\WhereClause' => 
  array (
    'type' => 'trait',
    'traitname' => 'WhereClause',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'LearnDash\\Instructor_Role\\StellarWP\\DB\\QueryBuilder\\Concerns\\WhereClause',
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
