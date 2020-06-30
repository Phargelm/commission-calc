## CommissionCalc
App is inteded to calculate commissions for already made transactions.
## Running
After pulling repository, install app dependencies, using `composer install`. Assuming your working directory is a project root, execute:
```
> php cli/run.php input.txt
1.00
0.45
1.66
2.33
43.84
```
In order to run unit tests, execute:
```
> composer test tests
PHPUnit 9.2.5 by Sebastian Bergmann and contributors.

....                                                                4 / 4 (100%)

Time: 00:00.021, Memory: 6.00 MB

OK (4 tests, 4 assertions)
```
## Notes about implementation
1. App components are structured to be reusable and flexible. You can tune it by modifying configs in `config/config.php`.
2. Only two major units are covered by unit tests. It is not enough for production and usually I try to cover as much as I can by unit tests, but, according to the exercise description, I assume that it will be enough to evaluate this task. If it is not enough, I can cover all modules by unit tests.
3. Note that according to ISO 4217, JPY is non-decimal currency. But in `input.txt`, provided by task description as an example, transaction in JPY with minor units can be found `{"bin":"45417360","amount":"10000.00","currency":"JPY"}`. Current implementation will round this amount **only for non-decimal currencies** like JPY during parsing (1000.89 JPY => 1001 JPY).
