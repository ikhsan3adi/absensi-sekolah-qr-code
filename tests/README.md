# Unit Tests untuk Absensi Sekolah QR Code

## Overview

Repository ini berisi comprehensive unit tests untuk aplikasi Absensi Sekolah QR Code. Tests mencakup:

- **82 Model tests** - Testing CRUD operations dan business logic
- **45 Helper tests** - Testing utility functions dan security
- **18 Validation tests** - Testing custom validation rules

**Total: 145 test cases**

## Quick Start

### Prerequisites

```bash
# Install dependencies
composer install

# Make sure PHP extensions are installed
php -m | grep -i sqlite  # For in-memory testing
```

### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run with readable output
vendor/bin/phpunit --testdox

# Run specific test suite
vendor/bin/phpunit tests/unit/Models/
vendor/bin/phpunit tests/unit/Helpers/
vendor/bin/phpunit tests/unit/Validation/

# Run single test file
vendor/bin/phpunit tests/unit/Models/SiswaModelTest.php

# Run specific test method
vendor/bin/phpunit --filter testCreateSiswaSuccessfully
```

## Test Structure

```
tests/
├── unit/
│   ├── Models/
│   │   ├── PresensiSiswaModelTest.php    (22 tests)
│   │   ├── PresensiGuruModelTest.php     (20 tests)
│   │   ├── SiswaModelTest.php            (25 tests)
│   │   └── GuruModelTest.php             (15 tests)
│   ├── Helpers/
│   │   └── CommonHelpersTest.php         (45 tests)
│   └── Validation/
│       └── RFIDRulesTest.php             (18 tests)
├── _support/                             (CodeIgniter test helpers)
└── README.md                             (this file)
```

## Test Coverage by Module

### Models (82 tests)

#### PresensiSiswaModel
- ✅ Check attendance existence
- ✅ Record entry/exit attendance
- ✅ Update attendance records
- ✅ Get attendance by student/date/class
- ✅ Handle duplicate entries
- ✅ Validate attendance status filters

#### PresensiGuruModel
- ✅ Check teacher attendance
- ✅ Record teacher entry/exit
- ✅ Update teacher attendance
- ✅ Get attendance by date
- ✅ Handle attendance modifications

#### SiswaModel
- ✅ Create/Read/Update/Delete operations
- ✅ Search by unique_code and rfid_code
- ✅ Filter by class and major
- ✅ Count students per class
- ✅ Verify unique code generation

#### GuruModel
- ✅ Create/Read/Update operations
- ✅ Search by unique_code and rfid_code
- ✅ List all teachers
- ✅ Verify hash-based unique code

### Helpers (45 tests)

#### String Manipulation
- ✅ `cleanStr()` - Remove special characters
- ✅ `clrQuotes()` - Remove quotes
- ✅ `strTrim()` - Trim whitespace
- ✅ `strReplace()` - String replacement
- ✅ `removeForbiddenCharacters()` - Security filtering
- ✅ `removeSpecialCharacters()` - Character cleaning

#### Number Processing
- ✅ `cleanNumber()` - Safe integer conversion
- ✅ Handle edge cases (empty, negative, overflow)

#### Utilities
- ✅ `generateToken()` - Unique token generation
- ✅ `countItems()` - Array counting
- ✅ `getCSVInputValue()` - CSV data extraction

#### Security Tests
- ✅ XSS prevention
- ✅ SQL injection prevention
- ✅ Path traversal prevention

### Validation (18 tests)

#### RFIDRules
- ✅ Unique RFID validation
- ✅ Cross-table checking (siswa & guru)
- ✅ Exclude ID for updates
- ✅ Handle empty/null values
- ✅ Edge cases (case sensitivity, special chars)

## Configuration

### Database

Tests are configured to use SQLite in-memory database for speed and isolation:

```xml
<!-- phpunit.xml.dist -->
<env name="database.tests.DBDriver" value="SQLite3"/>
<env name="database.tests.database" value=":memory:"/>
```

### Known Issues

⚠️ **Database-dependent tests** currently cannot run without proper database setup due to:
1. MySQL-specific migrations (raw SQL for indexes)
2. `after` clause in migrations (not supported in SQLite)

**Workaround options:**
1. Use MySQL test database
2. Create SQLite-compatible migrations
3. Mock database interactions for pure unit tests

See `TESTING.md` for detailed analysis and recommendations.

## Test Patterns Used

### AAA Pattern
```php
public function testCreateSiswa(): void
{
    // Arrange
    $data = ['nis' => '1001', 'nama' => 'John'];
    
    // Act
    $result = $this->model->createSiswa(...$data);
    
    // Assert
    $this->assertTrue($result);
}
```

### Happy & Unhappy Paths
```php
// Happy path
public function testCekSiswaWithValidCode(): void { ... }

// Unhappy path
public function testCekSiswaWithInvalidCode(): void { ... }
```

### Edge Cases
```php
public function testCreateSiswaGeneratesUniqueCode(): void { ... }
public function testDeleteMultiSelectedWithEmptyArray(): void { ... }
```

## Bugs Found During Testing

### 1. Logic Error in Attendance Filter
**File:** `PresensiSiswaModel.php` line 99, `PresensiGuruModel.php` line 96

```php
// ❌ Current (WRONG)
if ($value['id_kehadiran'] != ('1' || '2' || '3'))

// ✅ Should be
if (!in_array($value['id_kehadiran'], ['1', '2', '3']))
```

### 2. Duplicate Field Declaration
**File:** `GuruModel.php` line 48

```php
// ❌ Duplicate
'no_hp' => $noHp,
'no_hp' => $noHp,

// ✅ Remove duplicate
'no_hp' => $noHp,
```

### 3. SQL Injection Risk
**Files:** Various join queries in PresensiModels

```php
// ⚠️ Risk: Direct string interpolation
"tb_presensi_siswa.tanggal = '$tanggal'"

// ✅ Should use parameter binding
->where('tb_presensi_siswa.tanggal', $tanggal)
```

## Contributing Tests

### Writing New Tests

1. **Choose appropriate location:**
   - Models → `tests/unit/Models/`
   - Controllers → `tests/unit/Controllers/`
   - Libraries → `tests/unit/Libraries/`
   - Helpers → `tests/unit/Helpers/`

2. **Follow naming conventions:**
   ```php
   class ModelNameTest extends CIUnitTestCase
   {
       public function testMethodName_StateUnderTest_ExpectedBehavior(): void
       {
           // Test code
       }
   }
   ```

3. **Use descriptive assertions:**
   ```php
   $this->assertEquals($expected, $actual, 'Student name should match');
   ```

4. **Test one thing at a time:**
   ```php
   // ✅ Good
   public function testCreateSiswaSuccessfully(): void
   
   // ❌ Avoid
   public function testCreateAndUpdateAndDeleteSiswa(): void
   ```

### Test Checklist

- [ ] Test has clear, descriptive name
- [ ] Tests one specific behavior
- [ ] Includes happy path scenario
- [ ] Includes unhappy path scenario
- [ ] Includes edge cases if applicable
- [ ] Uses proper assertions
- [ ] Is isolated (doesn't depend on other tests)
- [ ] Cleans up after itself
- [ ] Runs fast (< 100ms for unit tests)

## Resources

- [CodeIgniter 4 Testing Docs](https://codeigniter.com/user_guide/testing/index.html)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Full Testing Analysis](../TESTING.md)
- [Original CI4 Testing Guide](./README_ORIGINAL.md)

## Support

For detailed testing documentation, analysis, and recommendations, see:
- **[TESTING.md](../TESTING.md)** - Complete testing documentation
- **[README_ORIGINAL.md](./README_ORIGINAL.md)** - Original CodeIgniter 4 testing guide

## License

This testing suite follows the same license as the main application.

---

**Last Updated:** December 28, 2025
**Tests Created By:** GitHub Copilot
**Framework:** CodeIgniter 4 + PHPUnit 9.6.24
