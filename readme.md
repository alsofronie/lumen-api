# Lumen-API

A scaffolding for Lumen Based API, featuring:

 - uri based version (with prefix `/api/v1`)
 - JSON Web Tokens authentication out of the box
 - Models with binary Uuid Primary key (version 4)
 - 100% Code Coverage for `/app` directory (wish)

### Features


#### Centralized API Exception

Although many will argue a centralized API Error codes approach, I found it
easier to manage in small / medium projects. So, everything the API
throws as an exception is dealt by the `ApiException` class, which has
all the internal API codes (maybe an HTML documentation for each code will
be nice) as static implementation.

The validation errors will be returned in the `details` component of the 
error, with the name of the validation rule. (to be completed).
