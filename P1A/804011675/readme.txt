Implementation:
Our implementation takes the user expression and puts it 
through two main steps:
	1) First, we dealt with all of the "Invalid Expression" 
	cases, by using "preg_match()" to detect any invalid patterns.
	2) If our implementation did not detect any invalid patterns, we concluded that the expression was safe to evaluate and used "eval()" to determine the result.

Work Division:
Our team used pair programming, where we both looked up php syntax/conventions whenever we needed to proceed on the project.