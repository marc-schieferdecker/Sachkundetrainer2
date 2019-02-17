# User

URL: https://waffensachkunde-trainer.de/Backend/Api/User/

## Actions

### FindByApiKey

Parameter

* string api_key

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/User/?action=FindByApiKey&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "user_id": 1,
    "user_userlevel": 30,
    "user_password": "xxx",
    "user_email": "ms@letsshootshow.de",
    "user_name": "Admin Beispielmandant",
    "user_api_key": "xxx",
    "user_count_wrong": 0,
    "user_count_right": 0,
    "user_config": "",
    "user_answered": ""
}
```

### FindById

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN, oder eigener Datensatz

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/User/?action=FindById&id=1&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "user_id": 1,
    "user_userlevel": 30,
    "user_password": "xxx",
    "user_email": "ms@letsshootshow.de",
    "user_name": "Admin Beispielmandant",
    "user_api_key": "xxx",
    "user_count_wrong": 0,
    "user_count_right": 0,
    "user_config": "",
    "user_answered": ""
}
```

### FindAll

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/User/?action=FindAll&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "user_id": 1,
        "user_userlevel": 10,
        "user_password": "xxx",
        "user_email": "ms@letsshootshow.de",
        "user_name": "Admin Beispielmandant",
        "user_api_key": "xxx",
        "user_count_wrong": 0,
        "user_count_right": 0,
        "user_config": "",
        "user_answered": ""
    },
    {
        "user_id": 2,
        "user_userlevel": 30,
        "user_password": "xxx",
        "user_email": "admin2@letsshootshow.de",
        "user_name": "User 2",
        "user_api_key": "xxx",
        "user_count_wrong": 0,
        "user_count_right": 0,
        "user_config": "",
        "user_answered": ""
    },
    {
        "user_id": 3,
        "user_userlevel": 20,
        "user_password": "xxx",
        "user_email": "admin3@letsshootshow.de",
        "user_name": "Client Admin 3",
        "user_api_key": "xxx",
        "user_count_wrong": 0,
        "user_count_right": 0,
        "user_config": "".
        "user_answered": ""
    }
]
```

### FindByFields

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Diese Suchparameter werden mit ODER verknüpft:

* string user_email
* string user_name

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/User/?action=FindByFields&user_name=Admin%&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "user_id": 1,
        "user_userlevel": 30,
        "user_password": "xxx",
        "user_email": "ms@letsshootshow.de",
        "user_name": "Admin Beispielmandant",
        "user_api_key": "xxx",
        "user_count_wrong": 0,
        "user_count_right": 0,
        "user_config": "",
        "user_answered": ""
    },
    {
        "user_id": 2,
        "user_userlevel": 30,
        "user_password": "xxx",
        "user_email": "admin2@letsshootshow.de",
        "user_name": "User 2",
        "user_api_key": "xxx",
        "user_count_wrong": 0,
        "user_count_right": 0,
        "user_config": "",
        "user_answered": ""
    }
]
```

### Add

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int user_userlevel
* string user_password (unsalted plain string)
* string user_email
* string user_name
* int user_count_wrong
* int user_count_right
* array user_config
* array user_answered

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/User/?action=Add&user_email=api@waffensachkunde-trainer.de&user_password=secret&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "user_id": 1,
    "user_userlevel": 30,
    "user_password": "xxx",
    "user_email": "ms@letsshootshow.de",
    "user_name": "Admin Beispielmandant",
    "user_api_key": "xxx",
    "user_count_wrong": 0,
    "user_count_right": 0,
    "user_config": "",
    "user_answered": ""
}
```

### Update

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN, oder eigener Datensatz

Parameter

* int id
* int user_userlevel (kann nicht höher als der eigene Userlevel des zugreifenden User sein)
* string user_password (unsalted plain string)
* string user_email
* string user_name
* int user_count_wrong
* int user_count_right
* array user_config
* array user_answered

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/User/?action=Update&id=999999&user_userlevel=10&user_email=api@waffensachkunde-trainer.de&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "user_id": 999999,
    "user_userlevel": 30,
    "user_password": "xxx",
    "user_email": "ms@letsshootshow.de",
    "user_name": "Admin Beispielmandant",
    "user_api_key": "xxx",
    "user_count_wrong": 0,
    "user_count_right": 0,
    "user_config": "",
    "user_answered": ""
}
```

### CreateApiKey

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN, oder eigener Datensatz

Erzeugt einen zufälligen neuen API Key für den Benutzer. Kann unabhängig von der Benutzerstufe immer für den eigenen Datensatz verwendet werden.

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/User/?action=CreateApiKey&id=999999&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "user_id": 999999,
    "user_userlevel": 30,
    "user_password": "xxx",
    "user_email": "ms@letsshootshow.de",
    "user_name": "Admin Beispielmandant",
    "user_api_key": "xxx",
    "user_count_wrong": 0,
    "user_count_right": 0,
    "user_config": "",
    "user_answered": ""
}
```

### Delete

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN, oder eigener Datensatz

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/User/?action=Delete&id=999999&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "user_id": 999999,
    "user_userlevel": 30,
    "user_password": "xxx",
    "user_email": "ms@letsshootshow.de",
    "user_name": "Admin Beispielmandant",
    "user_api_key": "xxx",
    "user_count_wrong": 0,
    "user_count_right": 0,
    "user_config": "",
    "user_answered": ""
}
```

### IncreaseCountWrong

Minimale Berechtigung: APPLICATION_USERLEVEL_USER

Erhöht den Zähler für eine falsche Antwort beim angemeldeten User um 1.

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=IncreaseCountWrong&id=3&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "user_id": 999999,
    "user_userlevel": 30,
    "user_password": "xxx",
    "user_email": "ms@letsshootshow.de",
    "user_name": "Admin Beispielmandant",
    "user_api_key": "xxx",
    "user_count_wrong": 1,
    "user_count_right": 0,
    "user_config": "",
    "user_answered": ""
}
```

### IncreaseCountRight

Minimale Berechtigung: APPLICATION_USERLEVEL_USER

Erhöht den Zähler für eine richtige Antwort beim angemeldeten User um 1.

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Question/?action=IncreaseCountRight3id=4&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "user_id": 999999,
    "user_userlevel": 30,
    "user_password": "xxx",
    "user_email": "ms@letsshootshow.de",
    "user_name": "Admin Beispielmandant",
    "user_api_key": "xxx",
    "user_count_wrong": 1,
    "user_count_right": 1,
    "user_config": "",
    "user_answered": ""
}
```
