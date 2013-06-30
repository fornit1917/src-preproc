src-preproc
=========

Simple script to process text files by means of c-like the directives (#include, #ifdef, #ifndef). Written in php.

# Usage 

`php src-preproc.php /path/to/input/file /path/to/output/file`

OR

`php src-preproc.php /path/to/input/file CONST1,CONST2,CONST3 /path/to/output/file`

* **/path/to/input/file** - file to be processed
* **/path/to/output/file** - this file will be the result of processing
* **CONST1,CONST2,CONST3** - list of constants names for #ifdef/#ifndef

The input file may contain directives #include, #ifdef and #ifndef

#### include

Can be specified in one of three ways:

* `//#include /path/to/file` 
* `##include /path/to/file` 
* `/*#include /path/to/file*/` 

#### ifdef and ifndef

Can be specified in one of three ways:

``` 
//#ifdef CONSTANT_NAME
...
//#endif
``` 

```
##ifdef CONSTANT_NAME
...
##endif
``` 

```
/*#ifdef CONSTANT_NAME */
...
/*#endif*/
```

If  CONSTANT_NAME specified in the list of constants when calling the script, 
the code is to be concluded between **#ifdef** and **#endif**, will be writed in the output file. 
Otherwise, he will be skipped. For **#ifndef** all conversely.

# Examples

input.js:

```javascript
(function a(){
  //#ifdef USE_MODULE_1
		//#include Module_1.js
		window.Module = Module_1;
  //#endif	

  //#ifdef USE_MODULE_2
		//#include Module_2.js
		window.Module = Module_2;
  //#endif
});
```

Run src-preproc with constant USE_MODULE_1:

`php src-preproc.php input.js USE_MODULE_1 output.js`

The file output.js will contain:

```javascript
(function a(){
		//contents of file Module_1.js
		//....
		window.Module = Module_1;
});
```

Run src-preproc with constant USE_MODULE_1:

`php src-preproc.php input.js USE_MODULE_2 output.js`

The file output.js will contain:

```javascript
(function a(){
		//contents of file Module_2.js
		//....
		window.Module = Module_2;
});
```