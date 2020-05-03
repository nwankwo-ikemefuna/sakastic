<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// REGULAR EXPRESSION EXAMPLES BY SITUATIONS AND NEEDS:

// Addresses

//Address: State code (US)
'/\\b(?:A[KLRZ]|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ADEINOST]|N[CDEHJMVY]|O[HKR]|PA|RI|S[CD]|T[NX]|UT|V[AT]|W[AIVY])\\b/'

//Address: ZIP code (US)
'\b[0-9]{5}(?:-[0-9]{4})?\b'
Columns

//Columns: Match a regex starting at a specific column on a line.
'^.{%SKIPAMOUNT%}(%REGEX%)'

//Columns: Range of characters on a line, captured into backreference 1
//Iterate over all matches to extract a column of text from a file
//E.g. to grab the characters in colums 8..10, set SKIPAMOUNT to 7, and CAPTUREAMOUNT to 3
'^.{%SKIPAMOUNT%}(.{%CAPTUREAMOUNT%})'
Credit cards

//Credit card: All major cards
'^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$'

//Credit card: American Express
'^3[47][0-9]{13}$'

//Credit card: Diners Club
'^3(?:0[0-5]|[68][0-9])[0-9]{11}$'

//Credit card: Discover
'^6011[0-9]{12}$'

//Credit card: MasterCard
'^5[1-5][0-9]{14}$'

//Credit card: Visa
'^4[0-9]{12}(?:[0-9]{3})?$'

//Credit card: remove non-digits
'/[^0-9]+/'
CSV

//CSV: Change delimiter
//Changes the delimiter from a comma into a tab.
//The capturing group makes sure delimiters inside double-quoted entries are ignored.
'("[^"\r\n]*")?,(?![^",\r\n]*"$)'

//CSV: Complete row, all fields.
//Match complete rows in a comma-delimited file that has 3 fields per row, 
//capturing each field into a backreference.  
//To match CSV rows with more or fewer fields, simply duplicate or delete the capturing groups.
'^("[^"\r\n]*"|[^,\r\n]*),("[^"\r\n]*"|[^,\r\n]*),("[^"\r\n]*"|[^,\r\n]*)$'

//CSV: Complete row, certain fields.
//Set %SKIPLEAD% to the number of fields you want to skip at the start, and %SKIPTRAIL% to 
//the number of fields you want to ignore at the end of each row.  
//This regex captures 3 fields into backreferences.  To capture more or fewer fields, 
//simply duplicate or delete the capturing groups.
'^(?:(?:"[^"\r\n]*"|[^,\r\n]*),){%SKIPLEAD%}("[^"\r\n]*"|[^,\r\n]*),("[^"\r\n]*"|[^,\r\n]*),("[^"\r\n]*"|[^,\r\n]*)(?:(?:"[^"\r\n]*"|[^,\r\n]*),){%SKIPTRAIL%}$'

//CSV: Partial row, certain fields
//Match the first SKIPLEAD+3 fields of each rows in a comma-delimited file that has SKIPLEAD+3 
//or more fields per row.  The 3 fields after SKIPLEAD are each captured into a backreference.  
//All other fields are ignored.  Rows that have less than SKIPLEAD+3 fields are skipped.  
//To capture more or fewer fields, simply duplicate or delete the capturing groups.
'^(?:(?:"[^"\r\n]*"|[^,\r\n]*),){%SKIPLEAD%}("[^"\r\n]*"|[^,\r\n]*),("[^"\r\n]*"|[^,\r\n]*),("[^"\r\n]*"|[^,\r\n]*)'

//CSV: Partial row, leading fields
//Match the first 3 fields of each rows in a comma-delimited file that has 3 or more fields per row.  
//The first 3 fields are each captured into a backreference.  All other fields are ignored.  
//Rows that have less than 3 fields are skipped.  To capture more or fewer fields, 
//simply duplicate or delete the capturing groups.
'^("[^"\r\n]*"|[^,\r\n]*),("[^"\r\n]*"|[^,\r\n]*),("[^"\r\n]*"|[^,\r\n]*)'

//CSV: Partial row, variable leading fields
//Match the first 3 fields of each rows in a comma-delimited file.  
//The first 3 fields are each captured into a backreference.
//All other fields are ignored.  If a row has fewer than 3 field, some of the backreferences 
//will remain empty.  To capture more or fewer fields, simply duplicate or delete the capturing groups.  
//The question mark after each group makes that group optional.
'^("[^"\r\n]*"|[^,\r\n]*),("[^"\r\n]*"|[^,\r\n]*)?,("[^"\r\n]*"|[^,\r\n]*)?'
Dates

//Date d/m/yy and dd/mm/yyyy
//1/1/00 through 31/12/99 and 01/01/1900 through 31/12/2099
//Matches invalid dates such as February 31st
'\b(0?[1-9]|[12][0-9]|3[01])[- /.](0?[1-9]|1[012])[- /.](19|20)?[0-9]{2}\b'

//Date dd/mm/yyyy
//01/01/1900 through 31/12/2099
//Matches invalid dates such as February 31st
'(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}'

//Date m/d/y and mm/dd/yyyy
//1/1/99 through 12/31/99 and 01/01/1900 through 12/31/2099
//Matches invalid dates such as February 31st
//Accepts dashes, spaces, forward slashes and dots as date separators
'\b(0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01])[- /.](19|20)?[0-9]{2}\b'

//Date mm/dd/yyyy
//01/01/1900 through 12/31/2099
//Matches invalid dates such as February 31st
'(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)[0-9]{2}'

//Date yy-m-d or yyyy-mm-dd
//00-1-1 through 99-12-31 and 1900-01-01 through 2099-12-31
//Matches invalid dates such as February 31st
'\b(19|20)?[0-9]{2}[- /.](0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01])\b'

//Date yyyy-mm-dd
//1900-01-01 through 2099-12-31
//Matches invalid dates such as February 31st
'(19|20)[0-9]{2}[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])'
Delimiters

//Delimiters: Replace commas with tabs
//Replaces commas with tabs, except for commas inside double-quoted strings
'((?:"[^",]*+")|[^,]++)*+,'
Email addresses

//Email address
//Use this version to seek out email addresses in random documents and texts.
//Does not match email addresses using an IP address instead of a domain name.
//Does not match email addresses on new-fangled top-level domains with more than 4 letters such as .museum.  
//Including these increases the risk of false positives when applying the regex to random documents.
'\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b'

//Email address (anchored)
//Use this anchored version to check if a valid email address was entered.
//Does not match email addresses using an IP address instead of a domain name.
//Does not match email addresses on new-fangled top-level domains with more than 4 letters such as .museum.
//Requires the "case insensitive" option to be ON.
'^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'

//Email address (anchored; no consecutive dots)
//Use this anchored version to check if a valid email address was entered.
//Improves on the original email address regex by excluding addresses with consecutive dots such as john@aol...com
//Does not match email addresses using an IP address instead of a domain name.
//Does not match email addresses on new-fangled top-level domains with more than 4 letters such as .museum.  
//Including these increases the risk of false positives when applying the regex to random documents.
'^[A-Z0-9._%-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$'

//Email address (no consecutive dots)
//Use this version to seek out email addresses in random documents and texts.
//Improves on the original email address regex by excluding addresses with consecutive dots such as john@aol...com
//Does not match email addresses using an IP address instead of a domain name.
//Does not match email addresses on new-fangled top-level domains with more than 4 letters such as .museum.  
//Including these increases the risk of false positives when applying the regex to random documents.
'\b[A-Z0-9._%-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}\b'

//Email address (specific TLDs)
//Does not match email addresses using an IP address instead of a domain name.
//Matches all country code top level domains, and specific common top level domains.
'^[A-Z0-9._%-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|biz|info|name|aero|biz|info|jobs|museum|name)$'

//Email address: Replace with HTML link
'\b(?:mailto:)?([A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})\b'
HTML

//HTML comment
'<!--.*?-->'

//HTML file
//Matches a complete HTML file.  Place round brackets around the .*? parts you want to extract from the file.
//Performance will be terrible on HTML files that miss some of the tags 
//(and thus won't be matched by this regular expression).  Use the atomic version instead when your search 
//includes such files (the atomic version will also fail invalid files, but much faster).
'<html>.*?<head>.*?<title>.*?</title>.*?</head>.*?<body[^>]*>.*?</body>.*?</html>'

//HTML file (atomic)
//Matches a complete HTML file.  Place round brackets around the .*? parts you want to extract from the file.
//Atomic grouping maintains the regular expression's performance on invalid HTML files.
'<html>(?>.*?<head>)(?>.*?<title>)(?>.*?</title>)(?>.*?</head>)(?>.*?<body[^>]*>)(?>.*?</body>).*?</html>'

//HTML tag
//Matches the opening and closing pair of whichever HTML tag comes next.
//The name of the tag is stored into the first capturing group.
//The text between the tags is stored into the second capturing group.
'<([A-Z][A-Z0-9]*)[^>]*>(.*?)</\1>'

//HTML tag
//Matches the opening and closing pair of a specific HTML tag.
//Anything between the tags is stored into the first capturing group.
//Does NOT properly match tags nested inside themselves.
'<%TAG%[^>]*>(.*?)</%TAG%>'

//HTML tag
//Matches any opening or closing HTML tag, without its contents.
'</?[a-z][a-z0-9]*[^<>]*>'
 

IP addresses

//IP address
//Matches 0.0.0.0 through 999.999.999.999
//Use this fast and simple regex if you know the data does not contain invalid IP addresses.
'\b([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\b'

//IP address
//Matches 0.0.0.0 through 999.999.999.999
//Use this fast and simple regex if you know the data does not contain invalid IP addresses, 
//and you don't need access to the individual IP numbers.
'\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b'

//IP address
//Matches 0.0.0.0 through 255.255.255.255
//Use this regex to match IP numbers with accurracy, without access to the individual IP numbers.
'\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b'

//IP address
//Matches 0.0.0.0 through 255.255.255.255
//Use this regex to match IP numbers with accurracy.
//Each of the 4 numbers is stored into a capturing group, so you can access them for further processing.
'\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b'
Lines

//Lines: Absolutely blank (no whitespace)
//Regex match does not include line break after the line.
'^$'

//Lines: Blank (may contain whitespace)
//Regex match does not include line break after the line.
'^[ \t]*$'

//Lines: Delete absolutely blank lines
//Regex match includes line break after the line.
'^\r?\n'

//Lines: Delete blank lines
//Regex match includes line break after the line.
'^[ \t]*$\r?\n'

//Lines: Delete duplicate lines
//This regex matches two or more lines, each identical to the first line.  
//It deletes all of them, except the first.
'^(.*)(\r?\n\1)+$'

//Lines: Truncate a line after a regex match.
//The regex you specify is guaranteed to match only once on each line.  
//If the original regex you specified should match more than once, 
//the line will be truncated after the last match.
preg_replace('^.*(%REGEX%)(.*)$', '$1$2', $text);

//Lines: Truncate a line before a regex match.
//If the regex matches more than once on the same line, everything before the last match is deleted.
preg_replace('^.*(%REGEX%)', '$1', $text);

//Lines: Truncate a line before and after a regex match.
//This will delete everything from the line not matched by the regular expression.
preg_replace('^.*(%REGEX%).*$', '$1', $text);
Logs

//Logs: Apache web server
//Successful hits to HTML files only.  Useful for counting the number of page views.
'^((?#client IP or domain name)\S+)\s+((?#basic authentication)\S+\s+\S+)\s+\[((?#date and time)[^]]+)\]\s+"(?:GET|POST|HEAD) ((?#file)/[^ ?"]+?\.html?)\??((?#parameters)[^ ?"]+)? HTTP/[0-9.]+"\s+(?#status code)200\s+((?#bytes transferred)[-0-9]+)\s+"((?#referrer)[^"]*)"\s+"((?#user agent)[^"]*)"$'

//Logs: Apache web server
//404 errors only
'^((?#client IP or domain name)\S+)\s+((?#basic authentication)\S+\s+\S+)\s+\[((?#date and time)[^]]+)\]\s+"(?:GET|POST|HEAD) ((?#file)[^ ?"]+)\??((?#parameters)[^ ?"]+)? HTTP/[0-9.]+"\s+(?#status code)404\s+((?#bytes transferred)[-0-9]+)\s+"((?#referrer)[^"]*)"\s+"((?#user agent)[^"]*)"$'
Numbers

//Number: Currency amount
//Optional thousands separators; optional two-digit fraction
'\b[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?\b'

//Number: Currency amount
//Optional thousands separators; mandatory two-digit fraction
'\b[0-9]{1,3}(?:,?[0-9]{3})*\.[0-9]{2}\b'

//Number: floating point
//Matches an integer or a floating point number with mandatory integer part.  The sign is optional.
'[-+]?\b[0-9]+(\.[0-9]+)?\b'

//Number: floating point
//Matches an integer or a floating point number with optional integer part.  The sign is optional.
'[-+]?\b[0-9]*\.?[0-9]+\b'

//Number: hexadecimal (C-style)
'\b0[xX][0-9a-fA-F]+\b'

//Number: Insert thousands separators
//Replaces 123456789.00 with 123,456,789.00
'(?<=[0-9])(?=(?:[0-9]{3})+(?![0-9]))'

//Number: integer
//Will match 123 and 456 as separate integer numbers in 123.456
'\b\d+\b'

//Number: integer
//Does not match numbers like 123.456
'(?<!\S)\d++(?!\S)'

//Number: integer with optional sign
'[-+]?\b\d+\b'

//Number: scientific floating point
//Matches an integer or a floating point number.
//Integer and fractional parts are both optional.
'[-+]?(?:\b[0-9]+(?:\.[0-9]*)?|\.[0-9]+\b)(?:[eE][-+]?[0-9]+\b)?'

//Number: scientific floating point
//Matches an integer or a floating point number with optional integer part.
//Both the sign and exponent are optional.
'[-+]?\b[0-9]*\.?[0-9]+(?:[eE][-+]?[0-9]+)?\b'
Passwords

//Password complexity
//Tests if the input consists of 6 or more letters, digits, underscores and hyphens.
//The input must contain at least one upper case letter, one lower case letter and one digit.
'\A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])[-_a-zA-Z0-9]{6,}\z'

//Password complexity
//Tests if the input consists of 6 or more characters.
//The input must contain at least one upper case letter, one lower case letter and one digit.
'\A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])\S{6,}\z'
File paths

//Path: Windows
'\b[a-z]:\\[^/:*?"<>|\r\n]*'

//Path: Windows
//Different elements of the path are captured into backreferences.
'\b((?#drive)[a-z]):\\((?#folder)[^/:*?"<>|\r\n]*\\)?((?#file)[^\\/:*?"<>|\r\n]*)'

//Path: Windows or UNC
'(?:(?#drive)\b[a-z]:|\\\\[a-z0-9]+)\\[^/:*?"<>|\r\n]*'

//Path: Windows or UNC
//Different elements of the path are captured into backreferences.
'((?#drive)\b[a-z]:|\\\\[a-z0-9]+)\\((?#folder)[^/:*?"<>|\r\n]*\\)?((?#file)[^\\/:*?"<>|\r\n]*)'
Phone numbers

//Phone Number (North America)
//Matches 3334445555, 333.444.5555, 333-444-5555, 333 444 5555, (333) 444 5555 and all combinations thereof.
//Replaces all those with (333) 444-5555
preg_replace('\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})', '(\1) \2-\3', $text);

//Phone Number (North America)
//Matches 3334445555, 333.444.5555, 333-444-5555, 333 444 5555, (333) 444 5555 and all combinations thereof.
'\(?[0-9]{3}\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}'
Postal codes

//Postal code (Canada)
'\b[ABCEGHJKLMNPRSTVXY][0-9][A-Z] [0-9][A-Z][0-9]\b'

//Postal code (UK)
'\b[A-Z]{1,2}[0-9][A-Z0-9]? [0-9][ABD-HJLNP-UW-Z]{2}\b'
Programming

//Programming: # comment
//Single-line comment started by # anywhere on the line
'#.*$'

//Programming: # preprocessor statement
//Started by # at the start of the line, possibly preceded by some whitespace.
'^\s*#.*$'

//Programming: /* comment */
//Does not match nested comments.  Most languages, including C, Java, C#, etc. 
//do not allow comments to be nested.  I.e. the first */ closes the comment.
'/\*.*?\*/'

//Programming: // comment
//Single-line comment started by // anywhere on the line
'//.*$'

//Programming: GUID
//Microsoft-style GUID, numbers only.
'[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}'

//Programming: GUID
//Microsoft-style GUID, with optional parentheses or braces.
//(Long version, if your regex flavor doesn't support conditionals.)
'[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}|\([A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\)|\{[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}'

//Programming: GUID
//Microsoft-style GUID, with optional parentheses or braces.
//Short version, illustrating the use of regex conditionals.  Not all regex flavors support conditionals.  
//Also, when applied to large chunks of data, the regex using conditionals will likely be slower 
//than the long version.  Straight alternation is much easier to optimize for a regex engine.
'(?:(\()|(\{))?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}(?(1)\))(?(2)\})'

//Programming: Remove escapes
//Remove backslashes used to escape other characters
preg_replace('\\(.)', '\1', $text);

//Programming: String
//Quotes may appear in the string when escaped with a backslash.
//The string may span multiple lines.
'"[^"\\]*(?:\\.[^"\\]*)*"'

//Programming: String
//Quotes may appear in the string when escaped with a backslash.
//The string cannot span multiple lines.
'"[^"\\\r\n]*(?:\\.[^"\\\r\n]*)*"'

//Programming: String
//Quotes may not appear in the string.  The string cannot span multiple lines.
'"[^"\r\n]*"'
Quotes

//Quotes: Replace smart double quotes with straight double quotes.
//ANSI version for use with 8-bit regex engines and the Windows code page 1252.
preg_replace('[\x84\x93\x94]', '"', $text);

//Quotes: Replace smart double quotes with straight double quotes.
//Unicode version for use with Unicode regex engines.
preg_replace('[\u201C\u201D\u201E\u201F\u2033\u2036]', '"', $text);

//Quotes: Replace smart single quotes and apostrophes with straight single quotes.
//Unicode version for use with Unicode regex engines.
preg_replace("[\u2018\u2019\u201A\u201B\u2032\u2035]", "'", $text);

//Quotes: Replace smart single quotes and apostrophes with straight single quotes.
//ANSI version for use with 8-bit regex engines and the Windows code page 1252.
preg_replace("[\x82\x91\x92]", "'", $text);

//Quotes: Replace straight apostrophes with smart apostrophes
preg_replace("\b'\b", "?", $text);

//Quotes: Replace straight double quotes with smart double quotes.
//ANSI version for use with 8-bit regex engines and the Windows code page 1252.
preg_replace('\B"\b([^"\x84\x93\x94\r\n]+)\b"\B', '?\1?', $text);

//Quotes: Replace straight double quotes with smart double quotes.
//Unicode version for use with Unicode regex engines.
preg_replace('\B"\b([^"\u201C\u201D\u201E\u201F\u2033\u2036\r\n]+)\b"\B', '?\1?', $text);

//Quotes: Replace straight single quotes with smart single quotes.
//Unicode version for use with Unicode regex engines.
preg_replace("\B'\b([^'\u2018\u2019\u201A\u201B\u2032\u2035\r\n]+)\b'\B", "?\1?", $text);

//Quotes: Replace straight single quotes with smart single quotes.
//ANSI version for use with 8-bit regex engines and the Windows code page 1252.
preg_replace("\B'\b([^'\x82\x91\x92\r\n]+)\b'\B", "?\1?", $text);
Escape

//Regex: Escape metacharacters
//Place a backslash in front of the regular expression metacharacters
preg_replace("[][{}()*+?.\\^$|]", "\\$0", $text);
Security

//Security: ASCII code characters excl. tab and CRLF
//Matches any single non-printable code character that may cause trouble in certain situations.
//Excludes tabs and line breaks.
'[\x00\x08\x0B\x0C\x0E-\x1F]'

//Security: ASCII code characters incl. tab and CRLF
//Matches any single non-printable code character that may cause trouble in certain situations.
//Includes tabs and line breaks.
'[\x00-\x1F]'

//Security: Escape quotes and backslashes
//E.g. escape user input before inserting it into a SQL statement
preg_replace("\\$0", "\\$0", $text);

//Security: Unicode code and unassigned characters excl. tab and CRLF
//Matches any single non-printable code character that may cause trouble in certain situations.
//Also matches any Unicode code point that is unused in the current Unicode standard, 
//and thus should not occur in text as it cannot be displayed.
//Excludes tabs and line breaks.
'[^\P{C}\t\r\n]'

//Security: Unicode code and unassigned characters incl. tab and CRLF
//Matches any single non-printable code character that may cause trouble in certain situations.
//Also matches any Unicode code point that is unused in the current Unicode standard, 
//and thus should not occur in text as it cannot be displayed.
//Includes tabs and line breaks.
'\p{C}'

//Security: Unicode code characters excl. tab and CRLF
//Matches any single non-printable code character that may cause trouble in certain situations.
//Excludes tabs and line breaks.
'[^\P{Cc}\t\r\n]'

//Security: Unicode code characters incl. tab and CRLF
//Matches any single non-printable code character that may cause trouble in certain situations.
//Includes tabs and line breaks.
'\p{Cc}'
SSN (Social security numbers)

//Social security number (US)
'\b[0-9]{3}-[0-9]{2}-[0-9]{4}\b'
Trim

//Trim whitespace (including line breaks) at the end of the string
preg_replace("\s+\z", "", $text);

//Trim whitespace (including line breaks) at the start and the end of the string
preg_replace("\A\s+|\s+\z", "", $text);

//Trim whitespace (including line breaks) at the start of the string
preg_replace("\A\s+", "", $text);

//Trim whitespace at the end of each line
preg_replace("[ \t]+$", "", $text);

//Trim whitespace at the start and the end of each line
preg_replace("^[ \t]+|[ \t]+$", "", $text);

//Trim whitespace at the start of each line
preg_replace("^[ \t]+", "", $text);
URL’s

//URL: Different URL parts
//Protocol, domain name, page and CGI parameters are captured into backreferenes 1 through 4
'\b((?#protocol)https?|ftp)://((?#domain)[-A-Z0-9.]+)((?#file)/[-A-Z0-9+&@#/%=~_|!:,.;]*)?((?#parameters)\?[-A-Z0-9+&@#/%=~_|!:,.;]*)?'

//URL: Different URL parts
//Protocol, domain name, page and CGI parameters are captured into named capturing groups.
//Works as it is with .NET, and after conversion by RegexBuddy on the Use page with Python, PHP/preg and PCRE.
'\b(?<protocol>https?|ftp)://(?<domain>[-A-Z0-9.]+)(?<file>/[-A-Z0-9+&@#/%=~_|!:,.;]*)?(?<parameters>\?[-A-Z0-9+&@#/%=~_|!:,.;]*)?'

//URL: Find in full text
//The final character class makes sure that if an URL is part of some text, punctuation such as a 
//comma or full stop after the URL is not interpreted as part of the URL.
'\b(https?|ftp|file)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]'

//URL: Replace URLs with HTML links
preg_replace('\b(https?|ftp|file)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]', '<a href="\0">\0</a>', $text);
Words

//Words: Any word NOT matching a particular regex
//This regex will match all words that cannot be matched by %REGEX%.
//Explanation: Observe that the negative lookahead and the \w+ are repeated together.  
//This makes sure we test that %REGEX% fails at EVERY position in the word, and not just at any particular position.
'\b(?:(?!%REGEX%)\w)+\b'

//Words: Delete repeated words
//Find any word that occurs twice or more in a row.
//Delete all occurrences except the first.
preg_replace('\b(\w+)(?:\s+\1\b)+', '\1', $text);

//Words: Near, any order
//Matches word1 and word2, or vice versa, separated by at least 1 and at most 3 words
'\b(?:word1(?:\W+\w+){1,3}\W+word2|word2(?:\W+\w+){1,3}\W+word1)\b'

//Words: Near, list
//Matches any pair of words out of the list word1, word2, word3, separated by at least 1 and at most 6 words
'\b(word1|word2|word3)(?:\W+\w+){1,6}\W+(word1|word2|word3)\b'

//Words: Near, ordered
//Matches word1 and word2, in that order, separated by at least 1 and at most 3 words
'\bword1(?:\W+\w+){1,3}\W+word2\b'

//Words: Repeated words
//Find any word that occurs twice or more in a row.
'\b(\w+)\s+\1\b'

//Words: Whole word
'\b%WORD%\b'

//Words: Whole word
//Match one of the words from the list
'\b(?:word1|word2|word3)\b'

//Words: Whole word at the end of a line
//Whitespace permitted after the word
'\b%WORD%\s*$'

//Words: Whole word at the end of a line
'\b%WORD%$'

//Words: Whole word at the start of a line
'^%WORD%\b'

//Words: Whole word at the start of a line
//Whitespace permitted before the word
'^\s*%WORD%\b'