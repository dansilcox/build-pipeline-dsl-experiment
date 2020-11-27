<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Exception\Parser\SyntaxException;
use Joist\Lexer\Token;
use Joist\Lexer\Location;

class Mapper
{
    /**
     * Convert a key-value array to an array of tokens
     *
     * @param array<Token>|array<string,array<Token>> $tokens
     *
     * @return array
     */
    public function fromArray(array $tokens): array
    {
        if (isset($tokens['tokens'])) {
            /** @var array<Token> $tokens */
            $tokens = $tokens['tokens'];
        }

        $tokenOutput = [];
        foreach ($tokens as $token) {
            // Already a Token object!
            if ($token instanceof Token) {
                $tokenOutput[] = $token;
                continue;
            }

            if (is_array($token)) {
                $tokenOutput[] = $this->hydrateToken($token);
            }
        }

        return $tokenOutput;
    }

    /**
     * Hydrate a token from a key/value array
     *
     * @param array $tokenArray
     *
     * @return Token
     */
    private function hydrateToken(array $tokenArray): Token
    {
        $expectedKeys = [
            'type'     => 'string',
            'lexeme'   => 'string',
            'location' => [
                'line'   => 'int',
                'col'    => 'int',
                'length' => 'int'
            ],
            'literal'  => '?string'
        ];
        if (!$this->validateArrayKeys($expectedKeys, $tokenArray)) {
            throw new SyntaxException('Invalid token array');
        }

        $location = new Location(
            $tokenArray['location']['line'],
            $tokenArray['location']['col'],
            $tokenArray['location']['length']
        );

        return new Token(
            $tokenArray['type'],
            $tokenArray['lexeme'],
            $location,
            $tokenArray['literal'] ?? null
        );
    }

    /**
     * Recursively validate that an array has the right keys and that values are the expected types
     *
     * @param array $expectedKeys
     * @param array $tokenArray
     *
     * @return bool valid or not
     */
    private function validateArrayKeys(array $expectedKeys, array $tokenArray): bool
    {
        foreach ($expectedKeys as $key => $type) {
            $nullable = strpos($type, '?') === 0;
            if (
                !array_key_exists($key, $tokenArray)
                && $nullable
            ) {
                throw new SyntaxException("Invalid token array: expected key $key ($type) not found");
            }

            if (is_array($type)) {
                return $this->validateArrayKeys($type, $tokenArray[$key]);
            }

            if (!$nullable && !isset($tokenArray[$key])) {
                throw new SyntaxException("Key $key ($type) is not nullable");
            }

            switch (strtolower(str_replace('?', '', $type))) {
                case 'string':
                    return is_string($tokenArray[$key]);

                case 'int':
                    return is_numeric($tokenArray[$key]);
                default:
                    throw new SyntaxException('Config error: invalid expected type ' . $type);
            }
        }
        return true;
    }
}
