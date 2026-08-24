<?php declare(strict_types = 1);

// odsl-/Users/alekseinovikov/projects/posting/app/Models/SocialAccount.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\SocialAccount
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.1-3824327657b3838f63c76aede7fe56e8fdc0e883a37c26f048d7a4383e41d946',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\SocialAccount',
        'filename' => '/Users/alekseinovikov/projects/posting/app/Models/SocialAccount.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\SocialAccount',
    'shortName' => 'SocialAccount',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int $user_id
 * @property Platform $platform
 * @property string $name
 * @property string|null $external_id
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property Carbon|null $token_expires_at
 * @property array<string, mixed>|null $meta
 * @property bool $is_sandbox
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */',
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '[\'user_id\', \'platform\', \'name\', \'external_id\', \'access_token\', \'refresh_token\', \'token_expires_at\', \'meta\', \'is_sandbox\', \'is_active\']',
            'attributes' => 
            array (
              'startLine' => 30,
              'endLine' => 41,
              'startTokenPos' => 57,
              'startFilePos' => 900,
              'endTokenPos' => 89,
              'endFilePos' => 1076,
            ),
          ),
        ),
      ),
      1 => 
      array (
        'name' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '[\'access_token\', \'refresh_token\']',
            'attributes' => 
            array (
              'startLine' => 42,
              'endLine' => 42,
              'startTokenPos' => 96,
              'startFilePos' => 1089,
              'endTokenPos' => 101,
              'endFilePos' => 1121,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 30,
    'endLine' => 84,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'casts' => 
      array (
        'name' => 'casts',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array<string, string>
 */',
        'startLine' => 51,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SocialAccount',
        'implementingClassName' => 'App\\Models\\SocialAccount',
        'currentClassName' => 'App\\Models\\SocialAccount',
        'aliasName' => NULL,
      ),
      'user' => 
      array (
        'name' => 'user',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return BelongsTo<User, $this>
 */',
        'startLine' => 67,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SocialAccount',
        'implementingClassName' => 'App\\Models\\SocialAccount',
        'currentClassName' => 'App\\Models\\SocialAccount',
        'aliasName' => NULL,
      ),
      'targets' => 
      array (
        'name' => 'targets',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return HasMany<PostTarget, $this>
 */',
        'startLine' => 75,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SocialAccount',
        'implementingClassName' => 'App\\Models\\SocialAccount',
        'currentClassName' => 'App\\Models\\SocialAccount',
        'aliasName' => NULL,
      ),
      'usesLiveApi' => 
      array (
        'name' => 'usesLiveApi',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 80,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SocialAccount',
        'implementingClassName' => 'App\\Models\\SocialAccount',
        'currentClassName' => 'App\\Models\\SocialAccount',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));