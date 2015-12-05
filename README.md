AdvancedHtmlDom
===
The goal of this project is to be a DOM-based drop-in replacement for PHP's simple html dom library.

How To Use
----
The same way as simple. If you use file/str_get_html then you don't need to change anything. If you are instantiating with `new simple_html_dom()` then you will need to change that to `new AdvancedHtmlDom()`

What's Different
----
Mostly just formatting (spaces) in the html.
This is added by DOM and there's no way around it. Some non-standard selectors have been dropped but many more standard ones have been added. For example: `img[src!=foo]` was removed because it's not a valid selector.
Added are things like `a + b` and `a ~ b` or even `a.foo:not(.bar)`

What's Better
----
- 10x-20x Performance increase
- Reduced memory requirement
- Support for many more css selectors

Features
----
* Supports full set of css pseudo selectors plus many jquery extras: :not, :has, :contains, :gt, :lt, :eq
* Use with css or xpath: $doc->find('h3 a'), $doc->find('//h3//a')
* Jquery-style functions replace, wrap, unwrap, before after
* Nodeset math: $doc->find('a')->minus($doc->find('.skip_me'))
* Lots more features that haven't been documented yet.

Changelog
----
- Added changelog
- Generated doc
- Code revision - again it is faster (f.e. " to ')