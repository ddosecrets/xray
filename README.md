# Project X-Ray

Project X-Ray is a crowdsourced effort sponsored by Distributed Denial of Secrets to identify the owners of offshore corporations who are contributing to global inequality. In many cases these corporate owners may have committed crimes beyond mere tax evasion. Many, but not all, of these offshore corporations file confidential updates to their respective corporate registries containing information on their officers and directors. Many of those secret documents are now available on this site. While the listed companies and individuals are often not the Ultimate Beneficial Owner (or UBO) of the offshore corporation, this information can still provide important clues to journalists and law enforcement officials.

Unfortunately, much of the information provided is not offered in any standard format, and is scanned, making typical Optical Character Recognition (OCR) technology a poor solution. And without functioning OCR, the information is impossible to search. That's where you come in: with enough volunteers around the world, the information can be digitized, standardized and verified using good old-fashioned typing. 

## This Repository

This is the codebase for [xray.ddosecrets.com](https://xray.ddosecrets.com/). The only change between this version and the live version are four lines at the start of `shared.inc.php` which define some paths and MySQL login information. These lines will need to be edited if you want to deploy X-ray locally for testing.

There are few dependencies - a recent version of PHP and MySQL, along with a webserver to host with, should be sufficient.

## Contributions

Bug fixes and feature improvements are most welcome! Please open an issue or make a pull request. To report security vulnerabilities, _please_ email **xray-security (at) ddosecrets.com** so we can promptly patch them.

## License

See `LICENSE` file.
