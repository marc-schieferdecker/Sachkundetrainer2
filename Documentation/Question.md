# Question

URL: https://waffensachkunde-trainer.de/Backend/Api/Question/

## Actions

### FindById

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=FindById&id=1&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "question_id": 1,
    "question_topic_id": 1,
    "question_subtopic_id": 1,
    "question_number": "1.01",
    "question_text": "Was regelt das Waffengesetz?",
    "question_count_wrong": 11,
    "question_count_right": 1112,
    "answeres": null,
    "topic": null,
    "subtopic": null
}
```

### FindByRandom

Parameter

* int topic_id
* int subtopic_id
* int hard
* int favourites

Ist der Parameter "hard" gesetzt, wird eine zufällige Frage aus den 100 Fragen zurückgegeben, die am häufigsten falsch beantwortet wurden.

Ist der Parameter "favourites" gesetzt, wird eine zufällige Frage aus den favorisierten Fragen des Nutzers zurückgegeben. Dies funktioniert erst ab der Nutzerstufe APPLICATION_USERLEVEL_USER.

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=FindByRandom&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "question_id": 1,
    "question_topic_id": 1,
    "question_subtopic_id": 1,
    "question_number": "1.01",
    "question_text": "Was regelt das Waffengesetz?",
    "question_count_wrong": 11,
    "question_count_right": 1112,
    "answeres": null,
    "topic": null,
    "subtopic": null
}
```

### FindAll

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

Parameter

* string sort_by
* string sort_order
* int limit

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=FindAll&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "question_id": 1,
        "question_topic_id": 1,
        "question_subtopic_id": 1,
        "question_number": "1.01",
        "question_text": "Was regelt das Waffengesetz?",
        "question_count_wrong": 11,
        "question_count_right": 1112,
        "answeres": null,
        "topic": null,
        "subtopic": null
    },
    {
        "question_id": 2,
        "question_topic_id": 1,
        "question_subtopic_id": 1,
        "question_number": "1.02",
        "question_text": "Wie werden Schusswaffen im Sinne des Waffengesetzes definiert?",
        "question_count_wrong": 345,
        "question_count_right": 4562,
        "answeres": null,
        "topic": null,
        "subtopic": null
    },
    {
        "question_id": 3,
        "question_topic_id": 1,
        "question_subtopic_id": 1,
        "question_number": "1.03",
        "question_text": "Welche der hier genannten Gegenstände sind Schusswaffen im Sinne des Waffengesetzes?",
        "question_count_wrong": 43,
        "question_count_right": 455,
        "answeres": null,
        "topic": null,
        "subtopic": null
    }
]
```

### Add

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int question_topic_id
* int question_subtopic_id
* string question_number
* string question_text
* int question_count_wrong
* int question_count_right

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=Add&question_name=Thema%204&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "question_id": 4,
    "question_topic_id": 1,
    "question_subtopic_id": 1,
    "question_number": "1.04",
    "question_text": "Welche der hier genannten Gegenstände sind Schusswaffen, bzw. ihnen gleichgestellte Gegenstände im Sinne des Waffengesetzes?",
    "question_count_wrong": 0,
    "question_count_right": 0,
    "answeres": null,
    "topic": null,
    "subtopic": null
}
```

### Update

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int id
* int question_topic_id
* int question_subtopic_id
* string question_number
* string question_text
* int question_count_wrong
* int question_count_right

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=Update&id=4&question_name=Erstes%20Thema&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "question_id": 4,
    "question_topic_id": 1,
    "question_subtopic_id": 1,
    "question_number": "1.04",
    "question_text": "Welche der hier genannten Gegenstände sind Schusswaffen, bzw. ihnen gleichgestellte Gegenstände im Sinne des Waffengesetzes?",
    "question_count_wrong": 0,
    "question_count_right": 0,
    "answeres": null,
    "topic": null,
    "subtopic": null
}
```

### Delete

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=Delete&id=4&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "question_id": 4,
    "question_topic_id": 1,
    "question_subtopic_id": 1,
    "question_number": "1.04",
    "question_text": "Welche der hier genannten Gegenstände sind Schusswaffen, bzw. ihnen gleichgestellte Gegenstände im Sinne des Waffengesetzes?",
    "question_count_wrong": 0,
    "question_count_right": 0,
    "answeres": null,
    "topic": null,
    "subtopic": null
}
```

### IncreaseCountWrong

Minimale Berechtigung: APPLICATION_USERLEVEL_USER

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=IncreaseCountWrong&id=3&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "question_id": 4,
    "question_topic_id": 1,
    "question_subtopic_id": 1,
    "question_number": "1.04",
    "question_text": "Welche der hier genannten Gegenstände sind Schusswaffen, bzw. ihnen gleichgestellte Gegenstände im Sinne des Waffengesetzes?",
    "question_count_wrong": 0,
    "question_count_right": 0,
    "answeres": null,
    "topic": null,
    "subtopic": null
}
```

### IncreaseCountRight

Minimale Berechtigung: APPLICATION_USERLEVEL_USER

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=IncreaseCountRight3id=4&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "question_id": 4,
    "question_topic_id": 1,
    "question_subtopic_id": 1,
    "question_number": "1.04",
    "question_text": "Welche der hier genannten Gegenstände sind Schusswaffen, bzw. ihnen gleichgestellte Gegenstände im Sinne des Waffengesetzes?",
    "question_count_wrong": 0,
    "question_count_right": 0,
    "answeres": null,
    "topic": null,
    "subtopic": null
}
```
