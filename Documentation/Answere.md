# Answere

URL: https://waffensachkunde-trainer.de/Backend/Api/Answere/

## Actions

### FindById

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Answere/?action=FindById&id=1&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "answere_id": 1,
    "answere_question_id": 1,
    "answere_number": "a)",
    "answere_choice": "Blasrohr",
    "answere_text": "",
    "answere_correct": 0
}
```

### FindByQuestionId

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

Parameter

* int id (question_id)

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Answere/?action=FindById&id=1&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "answere_id": 1,
        "answere_question_id": 1,
        "answere_number": "a)",
        "answere_choice": "Blasrohr",
        "answere_text": "",
        "answere_correct": 0
    },
    {
        "answere_id": 2,
        "answere_question_id": 1,
        "answere_number": "b)",
        "answere_choice": "F im Fünfeck",
        "answere_text": "",
        "answere_correct": 1
    },
    {
        "answere_id": 3,
        "answere_question_id": 1,
        "answere_number": "c)",
        "answere_choice": "Doppelflinte",
        "answere_text": "",
        "answere_correct": 0
    }
]
```

### FindAll

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Answere/?action=FindAll&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "answere_id": 1,
        "answere_question_id": 1,
        "answere_number": "a)",
        "answere_choice": "Blasrohr",
        "answere_text": "",
        "answere_correct": 0
    },
    {
        "answere_id": 2,
        "answere_question_id": 1,
        "answere_number": "b)",
        "answere_choice": "F im Fünfeck",
        "answere_text": "",
        "answere_correct": 1
    },
    {
        "answere_id": 3,
        "answere_question_id": 1,
        "answere_number": "c)",
        "answere_choice": "Doppelflinte",
        "answere_text": "",
        "answere_correct": 0
    }
]
```

### Add

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int answere_question_id
* string answere_number
* string answere_choice
* string answere_text
* int answere_correct

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Answere/?action=Add&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "answere_id": 4,
    "answere_question_id": 1,
    "answere_number": "d)",
    "answere_choice": "Soft-Air-Waffen",
    "answere_text": "",
    "answere_correct": 0
}
```

### Update

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int id
* int answere_question_id
* string answere_number
* string answere_choice
* string answere_text
* int answere_correct

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Answere/?action=Update&id=4&question_name=Erstes%20Thema&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "answere_id": 4,
    "answere_question_id": 1,
    "answere_number": "d)",
    "answere_choice": "Airsoft-Waffen",
    "answere_text": "",
    "answere_correct": 0
}
```

### Delete

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Answere/?action=Delete&id=4&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "answere_id": 4,
    "answere_question_id": 1,
    "answere_number": "d)",
    "answere_choice": "Airsoft-Waffen",
    "answere_text": "",
    "answere_correct": 0
}
```
