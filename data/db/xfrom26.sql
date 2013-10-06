
DROP TABLE IF EXISTS wordlist;

CREATE TABLE wordlist(
	id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	word VARCHAR(30) NOT NULL,
	profile ENUM('', 'A1', 'A2', 'B1', 'B2', 'C1', 'C2') NOT NULL,
	pos ENUM(
		'',
		'abbreviation',
		'short for',
		'adjective',
		'adverb',
		'conjunction',
		'determiner',
		'exclamation',
		'noun',
		'pronoun',
		'verb',
		'auxiliary verb',
		'phrasal verb',
		'modal verb',
		'suffix',
		'prefix',
		'phrase',
		'preposition',
		'number',
		'quantifier') NOT NULL, -- part of speech
	unit TINYINT NOT NULL, -- 0 = Intro Unit, 1 = Unit 01, ...
	is_double TINYINT(1) NOT NULL DEFAULT 0,
	len TINYINT NOT NULL,
	definition VARCHAR(255) NOT NULL,
	example VARCHAR(255),

	UNIQUE KEY(word, profile, pos),
	PRIMARY KEY(id)
);
