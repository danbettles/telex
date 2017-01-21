# Telex

*Telex* finds telephone numbers in text.

This project is an experiment.  Having written something horribly inflexible to deal with European telephone numbers, I wanted to see if I could write something relatively simple - and a little more elegant - that would detect *any* telephone number with a degree of precision.  *Telex* is certainly more elegant, and probably more useful, than my previous solution, but it's still not reliable.  It's a difficult problem to solve reliably - certainly for me.

You're very welcome to contribute.

## Usage

For now:

```php
use DanBettles\Telex\Telex;
use DanBettles\Telex\NumberFinder;
use DanBettles\Telex\CountryTelephoneNumberMatcherFactory;

$telex = new Telex(new NumberFinder(), new CountryTelephoneNumberMatcherFactory());
$matches = $telex->findAll('A UK landline number: (01234) 567 890.  A UK mobile number: +44 (0)7123 456 789.');
```

## TODO

* Encode patterns for area codes, and well-structured local numbers, whenever practicable to increase the precision of the matcher.
* Write more tests, especially for `Telex`.
* Handle multiple adjacent numbers.
* In the matcher, trim trailing non-numeric characters from the matched string?
