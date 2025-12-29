<?php

namespace Tests\Unit\Helpers;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class CommonHelpersTest extends CIUnitTestCase
{
    // =====================================================
    // TESTS FOR cleanStr()
    // =====================================================

    public function testCleanStrRemovesSpecialCharacters(): void
    {
        $input = "Hello#World!";
        $result = cleanStr($input);
        
        $this->assertEquals('HelloWorld', $result);
    }

    public function testCleanStrRemovesForbiddenCharacters(): void
    {
        $input = "test;value<script>";
        $result = cleanStr($input);
        
        $this->assertEquals('testvaluescript', $result);
    }

    public function testCleanStrTrimsWhitespace(): void
    {
        $input = "  hello world  ";
        $result = cleanStr($input);
        
        $this->assertEquals('hello world', $result);
    }

    // =====================================================
    // TESTS FOR cleanNumber()
    // =====================================================

    public function testCleanNumberConvertsStringToInt(): void
    {
        $input = "123";
        $result = cleanNumber($input);
        
        $this->assertIsInt($result);
        $this->assertEquals(123, $result);
    }

    public function testCleanNumberReturnsZeroForEmptyString(): void
    {
        $input = "";
        $result = cleanNumber($input);
        
        $this->assertEquals(0, $result);
    }

    public function testCleanNumberReturnsZeroForNonNumeric(): void
    {
        $input = "abc";
        $result = cleanNumber($input);
        
        $this->assertEquals(0, $result);
    }

    public function testCleanNumberTrimsWhitespace(): void
    {
        $input = "  456  ";
        $result = cleanNumber($input);
        
        $this->assertEquals(456, $result);
    }

    public function testCleanNumberHandlesNegativeNumbers(): void
    {
        $input = "-789";
        $result = cleanNumber($input);
        
        $this->assertEquals(-789, $result);
    }

    // =====================================================
    // TESTS FOR clrQuotes()
    // =====================================================

    public function testClrQuotesRemovesDoubleQuotes(): void
    {
        $input = 'Hello "World"';
        $result = clrQuotes($input);
        
        $this->assertEquals('Hello World', $result);
    }

    public function testClrQuotesRemovesSingleQuotes(): void
    {
        $input = "Hello 'World'";
        $result = clrQuotes($input);
        
        $this->assertEquals('Hello World', $result);
    }

    public function testClrQuotesRemovesBothQuoteTypes(): void
    {
        $input = '"Hello\' \'World"';
        $result = clrQuotes($input);
        
        $this->assertEquals('Hello World', $result);
    }

    // =====================================================
    // TESTS FOR strTrim()
    // =====================================================

    public function testStrTrimRemovesLeadingWhitespace(): void
    {
        $input = "   hello";
        $result = strTrim($input);
        
        $this->assertEquals('hello', $result);
    }

    public function testStrTrimRemovesTrailingWhitespace(): void
    {
        $input = "hello   ";
        $result = strTrim($input);
        
        $this->assertEquals('hello', $result);
    }

    public function testStrTrimHandlesEmptyString(): void
    {
        $input = "";
        $result = strTrim($input);
        
        $this->assertNull($result);
    }

    public function testStrTrimHandlesNull(): void
    {
        $input = null;
        $result = strTrim($input);
        
        $this->assertNull($result);
    }

    // =====================================================
    // TESTS FOR strReplace()
    // =====================================================

    public function testStrReplaceReplacesString(): void
    {
        $input = "Hello World";
        $result = strReplace("World", "PHP", $input);
        
        $this->assertEquals('Hello PHP', $result);
    }

    public function testStrReplaceHandlesEmptyString(): void
    {
        $input = "";
        $result = strReplace("test", "replace", $input);
        
        $this->assertNull($result);
    }

    public function testStrReplaceHandlesNull(): void
    {
        $input = null;
        $result = strReplace("test", "replace", $input);
        
        $this->assertNull($result);
    }

    // =====================================================
    // TESTS FOR removeForbiddenCharacters()
    // =====================================================

    public function testRemoveForbiddenCharactersRemovesSemicolon(): void
    {
        $input = "test;value";
        $result = removeForbiddenCharacters($input);
        
        $this->assertEquals('testvalue', $result);
    }

    public function testRemoveForbiddenCharactersRemovesAngularBrackets(): void
    {
        $input = "<script>alert('xss')</script>";
        $result = removeForbiddenCharacters($input);
        
        // Quotes and slashes are also removed by removeForbiddenCharacters
        $this->assertEquals('scriptalert(xss)script', $result);
    }

    public function testRemoveForbiddenCharactersRemovesMultipleCharacters(): void
    {
        $input = "test$%*value";
        $result = removeForbiddenCharacters($input);
        
        $this->assertEquals('testvalue', $result);
    }

    public function testRemoveForbiddenCharactersRemovesSlash(): void
    {
        $input = "test/path/value";
        $result = removeForbiddenCharacters($input);
        
        $this->assertEquals('testpathvalue', $result);
    }

    // =====================================================
    // TESTS FOR removeSpecialCharacters()
    // =====================================================

    public function testRemoveSpecialCharactersRemovesHash(): void
    {
        $input = "#hashtag";
        $result = removeSpecialCharacters($input);
        
        $this->assertEquals('hashtag', $result);
    }

    public function testRemoveSpecialCharactersRemovesExclamation(): void
    {
        $input = "Hello!";
        $result = removeSpecialCharacters($input);
        
        $this->assertEquals('Hello', $result);
    }

    public function testRemoveSpecialCharactersRemovesParentheses(): void
    {
        $input = "test(value)";
        $result = removeSpecialCharacters($input);
        
        $this->assertEquals('testvalue', $result);
    }

    public function testRemoveSpecialCharactersWithQuotesRemoval(): void
    {
        $input = "Hello 'World' \"Test\"";
        $result = removeSpecialCharacters($input, true);
        
        $this->assertEquals('Hello World Test', $result);
    }

    public function testRemoveSpecialCharactersWithoutQuotesRemoval(): void
    {
        $input = "Hello 'World'";
        $result = removeSpecialCharacters($input, false);
        
        $this->assertEquals('Hello World', $result);
    }

    // =====================================================
    // TESTS FOR generateToken()
    // =====================================================

    public function testGenerateTokenReturnsString(): void
    {
        $result = generateToken();
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testGenerateTokenGeneratesUniqueTokens(): void
    {
        $token1 = generateToken();
        $token2 = generateToken();
        
        $this->assertNotEquals($token1, $token2);
    }

    public function testGenerateTokenShortVersionShorter(): void
    {
        $longToken = generateToken(false);
        $shortToken = generateToken(true);
        
        $this->assertLessThan(strlen($longToken), strlen($shortToken));
    }

    public function testGenerateTokenContainsNoDotsOnlyDashes(): void
    {
        $token = generateToken();
        
        $this->assertStringNotContainsString('.', $token);
        $this->assertStringContainsString('-', $token);
    }

    // =====================================================
    // TESTS FOR countItems()
    // =====================================================

    public function testCountItemsReturnsCorrectCount(): void
    {
        $items = [1, 2, 3, 4, 5];
        $result = countItems($items);
        
        $this->assertEquals(5, $result);
    }

    public function testCountItemsReturnsZeroForEmptyArray(): void
    {
        $items = [];
        $result = countItems($items);
        
        $this->assertEquals(0, $result);
    }

    public function testCountItemsReturnsZeroForNull(): void
    {
        $items = null;
        $result = countItems($items);
        
        $this->assertEquals(0, $result);
    }

    public function testCountItemsReturnsZeroForNonArray(): void
    {
        $items = "not an array";
        $result = countItems($items);
        
        $this->assertEquals(0, $result);
    }

    // =====================================================
    // TESTS FOR getCSVInputValue()
    // =====================================================

    public function testGetCSVInputValueReturnsValue(): void
    {
        $array = ['name' => 'John', 'age' => '25'];
        $result = getCSVInputValue($array, 'name');
        
        $this->assertEquals('John', $result);
    }

    public function testGetCSVInputValueReturnsEmptyStringForMissingKey(): void
    {
        $array = ['name' => 'John'];
        $result = getCSVInputValue($array, 'missing', 'string');
        
        $this->assertEquals('', $result);
    }

    public function testGetCSVInputValueReturnsZeroForMissingKeyInt(): void
    {
        $array = ['name' => 'John'];
        $result = getCSVInputValue($array, 'missing', 'int');
        
        $this->assertEquals(0, $result);
    }

    public function testGetCSVInputValueReturnsEmptyForEmptyArray(): void
    {
        $array = [];
        $result = getCSVInputValue($array, 'key');
        
        $this->assertEquals('', $result);
    }

    public function testGetCSVInputValueHandlesNullArray(): void
    {
        $array = null;
        $result = getCSVInputValue($array, 'key');
        
        $this->assertEquals('', $result);
    }

    // =====================================================
    // EDGE CASES & SECURITY TESTS
    // =====================================================

    public function testCleanStrHandlesXSSAttempt(): void
    {
        $input = "<script>alert('XSS')</script>";
        $result = cleanStr($input);
        
        // Should remove dangerous characters
        $this->assertStringNotContainsString('<', $result);
        $this->assertStringNotContainsString('>', $result);
    }

    public function testCleanStrHandlesSQLInjectionAttempt(): void
    {
        $input = "'; DROP TABLE users; --";
        $result = cleanStr($input);
        
        // Should remove dangerous characters
        $this->assertStringNotContainsString(';', $result);
        $this->assertStringNotContainsString("'", $result);
    }

    public function testRemoveForbiddenCharactersHandlesPathTraversal(): void
    {
        $input = "../../../etc/passwd";
        $result = removeForbiddenCharacters($input);
        
        $this->assertStringNotContainsString('/', $result);
        $this->assertEquals('......etcpasswd', $result);
    }

    public function testGenerateTokenIsReasonablyLong(): void
    {
        $token = generateToken();
        
        // Should be at least 20 characters for security
        $this->assertGreaterThan(20, strlen($token));
    }
}
