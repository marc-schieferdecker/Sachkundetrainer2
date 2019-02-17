# Api

URL: https://waffensachkunde-trainer.de/Backend/Api/Api/

## Actions

### FindByLogin

Keine Voraussetzungen, die Methode ist ohne API Key aufrufbar.

Sie dient dem Login via Username/Password und gibt den API Key des Benutzers zurück, der z.B. wie eine Session verwendet werden kann.

Superuser können sich mit dem in der Konfiguration hinterlegten Superuser-Login und dem Superuser-Kennwort anmelden, dann wird der Benutzer mit der höchsten Berechtigungsstufe im Mandanten für die Anmeldung verwendet.

* string email
* string password

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Api/?action=FindByLogin&username=test@email.de&password=xxxxxxx)

**Response**

```json
{
    "api_key": "c0aa6c85ab5d92513398a28381c701e6"
}
```

### RequestPasswordReset

Keine Voraussetzungen, die Methode ist ohne API Key aufrufbar und sendet eine E-Mail mit dem statischen API Key des Nutzers, damit der Nutzer sich mit dem API anmelden kann, um sein Kennwort zurückzusetzen.

Gibt bei Erfolg nur die Benutzer ID zurück, anderfalls ist das Feld errorMsg/errorCode mit einer entsprechenden Fehlermeldung belegt.

* string email

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Api/?action=RequestPasswordReset&email=test@email.de)

**Response**

```json
{
    "user_id": 515
}
```
