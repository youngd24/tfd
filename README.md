# The Freight Depot

The Freight Depot, referred to here as "tfd", was a startup I worked at around 2003 and was loosely based on the application from a previous startup named Digiship.

## Why?

The code here is REALLY old, like 15+ years old, and likely won't even work on a modern system. It's here for posterity, partially for me, and for the rest of the Internet. It's also here to show people my coding style, especially those I might go to work for. Call it a REALLY OLD resume.

So, feel free to look around but beware, at the time I wasn't setting vi to convert tabs to whitespace, I'm not exactly sure why I didn't. 

## Structure

```
CVS             - Old CVS directory
CarrierData     - Data from carriers on rates, services, etc. Raw to be parsed
ChangeLog       - Um, a change log
Makefile        - Makefile
README          - An empty thing, guess I didn't want to tell anyone
README.md       - This file
VERSION         - Release version of the entire thing
apache          - Apache HTTP server related config files
database        - The database behind the whole thing
docs            - Misc docs
includes.mk     - I don't remember
interfaces      - Various interfaces, notable the EDI one
marketing       - Spam, spam and more spam.
modules         - Libraries to include in
quote.dtd       - DTD for LTL freight quotes
rating          - The shipment rating system
site            - The main web site
sysconfig       - Various system configs
test            - Tests
third-party     - Third-part things
tools           - Various tools
```

I'm trying to include README.md's in each directory as time goes on, mainly so I can see what I was doing at the time.

### Prerequisites

A time machine

### Installing

I have absolutely no idea how you'd install it

## Built With

* PHP
* Perl
* MySQL
* Visual Basic
* CzarLite rating service

## Authors

* **Darren Young** - youngd24@gmail.com

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
