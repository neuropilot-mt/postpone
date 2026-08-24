<?php declare(strict_types = 1);

// osfsl-/Users/alekseinovikov/projects/posting/vendor/composer/../guzzlehttp/promises/src/PromiseInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-GuzzleHttp\Promise\PromiseInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-4c7f1b1c9a29ba1c8183970593bfbbc6192106d2167c094d4064dd3033fec6f7-8.4.1-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'filename' => '/Users/alekseinovikov/projects/posting/vendor/composer/../guzzlehttp/promises/src/PromiseInterface.php',
      ),
    ),
    'namespace' => 'GuzzleHttp\\Promise',
    'name' => 'GuzzleHttp\\Promise\\PromiseInterface',
    'shortName' => 'PromiseInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A promise represents the eventual result of an asynchronous operation.
 *
 * The primary way of interacting with a promise is through its then method,
 * which registers callbacks to receive either a promise’s eventual value or
 * the reason why the promise cannot be fulfilled.
 *
 * @template TValue = mixed
 * @template TReason = mixed
 *
 * @see https://promisesaplus.com/
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 108,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'PENDING' => 
      array (
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'name' => 'PENDING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pending\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 31,
            'startFilePos' => 510,
            'endTokenPos' => 31,
            'endFilePos' => 518,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 37,
      ),
      'FULFILLED' => 
      array (
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'name' => 'FULFILLED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'fulfilled\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 42,
            'startFilePos' => 550,
            'endTokenPos' => 42,
            'endFilePos' => 560,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'REJECTED' => 
      array (
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'name' => 'REJECTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'rejected\'',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 53,
            'startFilePos' => 591,
            'endTokenPos' => 53,
            'endFilePos' => 600,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'then' => 
      array (
        'name' => 'then',
        'parameters' => 
        array (
          'onFulfilled' => 
          array (
            'name' => 'onFulfilled',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 40,
                'endLine' => 40,
                'startTokenPos' => 72,
                'startFilePos' => 1586,
                'endTokenPos' => 72,
                'endFilePos' => 1589,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'callable',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 9,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'onRejected' => 
          array (
            'name' => 'onRejected',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 41,
                'endLine' => 41,
                'startTokenPos' => 82,
                'startFilePos' => 1624,
                'endTokenPos' => 82,
                'endFilePos' => 1627,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'callable',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 9,
            'endColumn' => 36,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'GuzzleHttp\\Promise\\PromiseInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Appends fulfillment and rejection handlers to the promise, and returns
 * a new promise resolving to the return value of the called handler.
 *
 * @template TFulfilledValue = never
 * @template TFulfilledReason = never
 * @template TRejectedValue = never
 * @template TRejectedReason = never
 *
 * @param (callable(TValue): (TFulfilledValue|PromiseInterface<TFulfilledValue, TFulfilledReason>))|null $onFulfilled Invoked when the promise fulfills.
 * @param (callable(TReason): (TRejectedValue|PromiseInterface<TRejectedValue, TRejectedReason>))|null   $onRejected  Invoked when the promise is rejected.
 *
 * @return PromiseInterface<($onFulfilled is null ? TValue : TFulfilledValue)|($onRejected is null ? never : TRejectedValue), ($onFulfilled is null ? never : TFulfilledReason|\\Throwable)|($onRejected is null ? TReason : TRejectedReason|\\Throwable)>
 */',
        'startLine' => 39,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 24,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'GuzzleHttp\\Promise',
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'currentClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'aliasName' => NULL,
      ),
      'otherwise' => 
      array (
        'name' => 'otherwise',
        'parameters' => 
        array (
          'onRejected' => 
          array (
            'name' => 'onRejected',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 57,
            'endLine' => 57,
            'startColumn' => 31,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'GuzzleHttp\\Promise\\PromiseInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Appends a rejection handler callback to the promise, and returns a new
 * promise resolving to the return value of the callback if it is called,
 * or to its original fulfillment value if the promise is instead
 * fulfilled.
 *
 * @template TRejectedValue = never
 * @template TRejectedReason = never
 *
 * @param callable(TReason): (TRejectedValue|PromiseInterface<TRejectedValue, TRejectedReason>) $onRejected Invoked when the promise is rejected.
 *
 * @return PromiseInterface<TValue|TRejectedValue, TRejectedReason|\\Throwable>
 */',
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 70,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'GuzzleHttp\\Promise',
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'currentClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'aliasName' => NULL,
      ),
      'getState' => 
      array (
        'name' => 'getState',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the state of the promise ("pending", "rejected", or "fulfilled").
 *
 * The three states can be checked against the constants defined on
 * PromiseInterface: PENDING, FULFILLED, and REJECTED.
 *
 * @return self::PENDING|self::FULFILLED|self::REJECTED
 */',
        'startLine' => 67,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 39,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'GuzzleHttp\\Promise',
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'currentClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'aliasName' => NULL,
      ),
      'resolve' => 
      array (
        'name' => 'resolve',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 76,
                'endLine' => 76,
                'startTokenPos' => 133,
                'startFilePos' => 2948,
                'endTokenPos' => 133,
                'endFilePos' => 2951,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 76,
            'endLine' => 76,
            'startColumn' => 29,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve the promise with the given value, or with null if no value is given.
 *
 * @param TValue|PromiseInterface<TValue, TReason>|null $value
 *
 * @throws \\RuntimeException if the promise is already resolved.
 */',
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 49,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'GuzzleHttp\\Promise',
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'currentClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'aliasName' => NULL,
      ),
      'reject' => 
      array (
        'name' => 'reject',
        'parameters' => 
        array (
          'reason' => 
          array (
            'name' => 'reason',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 85,
            'endLine' => 85,
            'startColumn' => 28,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Reject the promise with the given reason.
 *
 * @param TReason $reason
 *
 * @throws \\RuntimeException if the promise is already resolved.
 */',
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 42,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'GuzzleHttp\\Promise',
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'currentClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'aliasName' => NULL,
      ),
      'cancel' => 
      array (
        'name' => 'cancel',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Cancels the promise if possible.
 *
 * @see https://github.com/promises-aplus/cancellation-spec/issues/7
 */',
        'startLine' => 92,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 35,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'GuzzleHttp\\Promise',
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'currentClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'aliasName' => NULL,
      ),
      'wait' => 
      array (
        'name' => 'wait',
        'parameters' => 
        array (
          'unwrap' => 
          array (
            'name' => 'unwrap',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 107,
                'endLine' => 107,
                'startTokenPos' => 183,
                'startFilePos' => 3913,
                'endTokenPos' => 183,
                'endFilePos' => 3916,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 107,
            'endLine' => 107,
            'startColumn' => 26,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Waits until the promise completes if possible.
 *
 * Pass $unwrap as true to unwrap the result of the promise, either
 * returning the resolved value or throwing the rejected exception.
 *
 * If the promise cannot be waited on, then the promise will be rejected.
 *
 * @return ($unwrap is true ? TValue : null)
 *
 * @throws \\LogicException if the promise has no wait function or if the
 *                         promise does not settle after waiting.
 */',
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 46,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'GuzzleHttp\\Promise',
        'declaringClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'implementingClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
        'currentClassName' => 'GuzzleHttp\\Promise\\PromiseInterface',
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