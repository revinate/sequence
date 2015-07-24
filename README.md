# Sequence

This Symfony2-compatible library adds functional programming styles to PHP

## Sequence:
A sequence is an ordered collection of things (key-value pairs).  It can be created from an array, object, or iterator.  Depending upon the source, the key can be implied (the index into an array) or explicit (the field name for an object or assoc array).  Behind the scenes, it is just an iterator and uses the build in SPL iterator classes.

### The Methods:
There is a basic set of things you can do to a sequence: map, filter, and reduce.  The other method are really just specialized versions of these operations.  These methods take functions as parameters that will be applied to the data in the Sequence.  In general functions have the following signature: function ($value, $key) and return a value.

#### Map
This allows you to change the value or key into something different.  For example, you would use map to convert hotel_id's into Hotel objects.
    
- **map** - sets a new value based upon the value returned by the given function.  Signature: `function ($value, $key)`
- **mapKeys** - sets a new key based upon result returned by the given function. Signature: `function ($key, $value)`
- **keyBy** - is an alias for mapKeys but with the signature flipped.  Signature: `function ($value, $key)`

#### Filter
This allows you to filter out keys or values based upon some condition.

- **filter** - filters out all values where the given function returns a [falsy](http://php.net/manual/en/language.types.boolean.php) value. Signature: *function ($value, $key)*
- **filterKeys** - is just a special version of filter that changes the parameter order from *($value, $key)* to *($key, $value)*.  Signature: *function ($key, $value)*

#### Reduce
This allows you to convert the entire sequence into something.  This one take some getting used to, but is super powerful.

- to_a() - converts the entire sequence into an array.  This is a form of reduce, but it is implemented using *iterator_to_array* for speed.

### FnGen stuff:
FnGen is a set of functions that generate functions.  These were written to address common function patterns.

- FnGen::fnKeepInMap($lookUpTable) - generates a function that when called with a value, will return true if that value is a key in $lookUpTable, otherwise false.
One thing to keep in mind is that Sequences are lazy evaluated.  Nothing happens before the Sequence is consumed.

### Consuming a sequence:
Sequences should only be consumed once.  In many cases, it is possible to rewind them and consume them again, but I would discourage that practice.
Things that will consume a sequence:

- **foreach ($sequence as $key => $value)**
- **to_a()**        - converts the entire result into an array or assoc array depending upon the keys.
- **reduce()**      - convert all the items in the sequence into a single object.
- **walk()**        - for each item in the sequence, apply a function.  The return value is ignored. 
- **sort()**        - at the moment any of the sort methods will consume the entire sequence and generate a new sequence.  It is possible to make this lazy as well, but it required a lot more effort to write.
- **flattenOnceNow()** - like flattenOnce, but it does it immediately. 

