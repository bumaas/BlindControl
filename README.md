# Blind Control

Modul für Symcon ab Version 5.1.

Steuert einen Rollladen nach vorgegebenen Einstellungen.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Installation](#3-installation)  
4. [Funktionsreferenz](#4-funktionsreferenz)
5. [Konfiguration](#5-konfiguration)  
6. [Statusvariablen und Profile](#6-statusvariablen-und-profile)  
7. [Anhang](#7-anhang)  

## 1. Funktionsumfang

Das Blind Control Modul dient der Steuerung von Rollläden oder anderweitigen Abdunkelungseinrichtungen.

Aktuelle Features:

- Hoch-/Runterfahren zu vorgegeben Zeiten
- Urlaubs- und Feiertagsberücksichtigung
- Berücksichtigung Sonnenauf- und untergang
- Sonnenschutz inclusive Nachführen nach Sonnenstand
- Unterstützung von Kontakten (Fenster-/Tür, Regen, Alarm etc)
- Deaktivierungsmöglichkeit
- Berücksichtigung der Helligkeit
- Hitzeschutz
- Erkennung von manueller Bedienung
- Aktivierung/Deaktivierung über Statusvariable
- Notfall Sensor
- herstellerunabhängig

Noch nicht unterstützt wird:
- Lamellenstellung bei Stores
- Zufallsfunktion bei Tagesanfang/Tagesende


## 2. Voraussetzungen

 - Symcon 5.1
 
 - Es werden alle Aktoren unterstützt, die über eine Statusvariable verfügen und sich über RequestAction steuern lassen.
Die Statusvariable muss vom Typ Integer oder Float sein und ein Profil mit einem korrekten Minimal- und Maximalwert besitzen. Bei einem Rollladen, der beim Minimalwert 
geschlossen und beim Maximalwert geöffnet ist (z.B. typischerweise bei Homematic), ist ein Profil mit der Namensendung ".Reversed" zu verwenden.  

## 3. Installation

### a. Laden des Moduls

Das Modul wird über den Modul Store installiert.

### b. Anlegen einer Rollladen Instanz

In Symcon an beliebiger Stelle `Instanz hinzufügen` auswählen und `Blind Controller` auswählen. Es wird eine Rollladeninstanz angelegt, in der die Eigenschaften zur Steuerung eines einzelnen Rollladens gesetzt werden.

### c. Anlegen eines Gruppen Masters

In Symcon an beliebiger Stelle `Instanz hinzufügen` auswählen und `Blind Control Group Master` auswählen. Es wird ein Gruppen Master angelegt, in dem Rollläden zu Bearbeitungszwecken (nicht zu Steuerungszwecken!) zusammengefasst werden können.
Hierüber wird es ermöglicht, eine Eigenschaft für mehrere Rollläden in einem Schritt auszulesen oder zu setzen.

Der Gruppen Master dient zur leichteren Bearbeitung der definierten Rollladen Instanzen. 
 	

## 4. Funktionsreferenz

```php
BLC_ControlBlind(int $InstanceID, bool $considerDeactvationTimes)
```
Prüft die Rollladenposition gemäß der in der Instanz festgelegten Eigenschaften und fährt den Rollladen auf die ermittelte Position. Wenn $considerDeactivationTimes == true, dann wird DeactivationAutomaticMovement berücksichtigt.

```php
BLC_MoveBlind(int $InstanceID, int $percentClose, int $deactivationTimeAuto): bool
```
Fährt den Rollladen auf den gewünschten Schließungsgrad.
$percentClose: 0 - 100
Angabe des Schließungsgrades (0=geöffnet, 100 = geschlossen)
$deactivationTimeAuto: Anzahl der Sekunden, die mindestens seit der letzten automatischen Bewegung vergangen sein müssen. Sonst wird der Rollladen nicht bewegt.

```php
BLCGM_GetBlinds(int $InstanceID): array
```
Liefert eine Liste der im Gruppenmaster gelisteten Rollläden. Es werden nur die als ausgewählt markierten Einträge geliefert.

```php
BLCGM_GetPropertyOfBlinds(int $InstanceID, $Property): array
```
Liefert in einer Liste die gewählte Eigenschaft von allen als ausgewählt markierten Rollläden.

```php
BLCGM_SetPropertyOfBlinds(int $InstanceID, string $Property, $Value): bool
```
Setzt die angegebene Eigenschaft $Property auf den gegebenen Wert $Value bei allen Rollläden, die als ausgewählt markiert sind.

```php
BLCGM_SetBlindsActive(int $InstanceID, bool $active)
```
Setzt die Statusvariable 'Activated' auf den gegebenen Wert $active bei allen Rollläden, die als ausgewählt markiert sind.


## 5. Konfiguration

### Überprüfen, ob der zu steuernde Rollladen korrekt in IP-Symcon eingerichtet ist

Damit das Modul korrekt arbeiten kann, ist eine richtige und vollständige Einrichtung des zu steuernden Rollladens in IP-Symcon Voraussetzung. Es muss sichergestellt sein, dass der Rollladen sich korrekt positionieren lässt (offen, geschlossen und in Zwischenstufen) und die Laufrichtung richtig erkannt wird.
Dies lässt sich am einfachsten überprüfen, indem die zu steuernde Positions Variable (bei Homematic z.B. LEVEL genannt) mit einem adaptiven Icon (z.B. "Jalousie") versehen wird und das Webfront eingebunden wird.

Nun sollte sich im Webfront folgendes Bild für einen geöffneten bzw. geschlossenen Rollladen ergeben:

![image](docs/Rollladen geöffnet.jpg)
![image](docs/Rollladen geschlossen.jpg)

Zeigt das Icon den falschen Zustand an, dann ist dem Profil im Namen ein '.Reversed' anzuhängen. Man erreicht dies, indem ein bestehendes Profil kopiert wird und dabei der Name ergänzt wird.

Diese Positionsvariable ist im Modul als 'Rollladen Level ID' anzugeben.


### Einrichtung des Wochenplans
Für die Fahrzeiten ist ein Wochenplan Ereignis anzulegen mit folgenden Einstellungen:
 
![image](docs/Wochenplan.jpg)

Das Modul holt aus dem Wochenplan ausschließlich die Aktionszeiten und den Aktionstyp.

**Wichtig:** 
- der Wochenplan muss genau zwei Aktionen mit ID 1 und ID 2 beinhalten. Die eigentlichen Aktionen bleiben dabei jedoch leer, da der Wochenplan nicht von IP-Symcon direkt ausgeführt werden soll.
- Die Aktion mit ID 1 stellt dabei die Aktion zum Runterfahren des Rollladens und die ID 2 die Aktion zum Hochfahren des Rollladens dar.
- Es darf nur maximal einen Zeitraum zur Aktion 2 (Hochfahren) geben.
- ob ein Wochenplan aktiv ist oder nicht wird nicht berücksichtigt

Über den Wochenplan werden die Grundfahrzeiten (morgens hoch/ abends runter) definiert.

### Tagerkennung (optional)
Als Ergänzung zum Wochenplan kann eine zusätzliche Tagerkennung eingerichtet werden. Sie kommt zum Einsatz, wenn neben den Fahrzeiten gemäß Wochenplan auch die Helligkeit berücksichtigt werden soll.

Beispiel:
 
Der Rollladen wird gemäß Wochenplan morgens um 8:00 Uhr hochgefahren und abends um 23:00 Uhr wieder herunter.
Er soll aber nur dann hochgefahren werden, wenn es Tag ist und nur dann heruntergefahren werden, wenn es nicht mehr Tag ist.

Hierzu kann die Tagerkennung zusätzlich eingerichtet werden. Dann ist der Rollladen nur dann hochgefahren, wenn beide Bedingungen (Öffnungszeit laut Wochenplan und "es ist Tag") erfüllt sind.

Damit der Tag erkannt wird, kann entweder auf eine bereits bestehenden Variable verwiesen werden (hier bietet sich die 'IsDay' Variable des Location Moduls an) oder durch einen Helligkeitsvergleich erfolgen.
Für einen Helligkeitsvergleich ist die Variable anzugeben, die den aktuellen Helligkeitswert beinhaltet (z.B. von einem Helligkeitssensor) sowie eine Variable, die den Schwellwert beinhaltet. Soll als Helligkeitswert ein Durchschnittswert der letzten Minuten genommen werden,
dann ist die Anzahl der Minuten anzugeben, über die der Durchschnitt gebildet werden soll. Der Durchschnitt wird aus den archivierten Daten gewonnen. Dazu ist es notwendig, dass für die Variable die Archivierung aktiviert ist.
 
 
![image](docs/LUX Messwert.jpg)

![image](docs/Helligkeitsschwellwert.jpg)

#### Übersteuernde Tagesanfang- und Endezeiten (optional)
Als zusätzliche Option kann auch eine übersteuernde feste Tagesanfangszeit und/oder Tagesendezeit angegeben werden. Dazu ist auf eine Variable zu verweisen, die die entsprechende Zeit im Format 'HH:MM' beinhaltet.
Die Zeiten übersteuern die in der Tagerkennung ermittelten Zeiten.
 

### Beschattung nach Sonnenstand (optional)
Es sind die Variablen anzugeben, aus denen der Sonnenstand (Azimuth = Sonnenrichtung, Altitude = Sonnenhöhe) geholt werden soll. Hier bieten sich gleichnamigen Variablen des Location Moduls an.

Des weiteren ist der Bereich (Azimuth von/bis) der Sonnenrichtung anzugeben, in dem die Beschattung stattfinden soll.
Für einen Helligkeitsvergleich ist die Variable anzugeben, die den aktuellen Helligkeitswert beinhaltet (z.B. von einem Helligkeitssensor) sowie eine Variable, die den Schwellwert beinhaltet. Soll als Helligkeitswert ein Durchschnittswert der letzten Minuten genommen werden,
dann ist die Anzahl der Minuten anzugeben, über die der Durchschnitt gebildet werden soll. Der Durchschnitt wird aus den archivierten Daten gewonnen. Dazu ist es notwendig, dass für die Variable die Archivierung aktiviert ist.

Zusätzlich kann eine Temperaturvariable angegeben werden, um bei erhöhten Außentemperaturen eine höhere Beschattung zu erreichen, d.h., der Rollladen wird bei höheren Temperaturen weiter heruntergefahren.
Dies erfolgt in zwei Stufen: Wenn die Temperatur 27°C übersteigt, wird der Rollladen um weitere 15% heruntergefahren, wenn die Temperatur 30°C übersteigt, dann wird der Rollladen auf eine Höhe von 10% heruntergefahren)

Um den richtige Behanghöhe bei unterschiedlichen Sonnenhöhen zu finden, sind die gewünschten Behanghöhen bei zwei (möglichst extremen) Sonnenhöhen anzugeben. Aus diesen beiden Positionen wird dann die 
Behanghöhe in Abhängigkeit von der Sonnenhöhe errechnet. Die Werte sind am besten durch Aufzeichnungen in der Mittagszeit/Abendzeit oder im Hochsommer/Winter zu ermitteln.

Durch eine korrekte Einmessung wird erreicht, dass der Schatten des Rollladens immer gleich weit im Raum steht und somit eine gleichmäßige Beschattung stattfindet.

### Beschattung nach Helligkeit (optional)
Es sind die Variablen anzugeben, die zur Helligkeitsbestimmung herangezogen werden sollen. Wenn der Helligkeitswert überschritten wird, dann wird der Rollladen auf die vorgegebene Position gefahren. Es stehen zwei Paare an Helligkeitsschwellwert und Rollladenposition zur Verfügung.
Die Beschattung nach Helligkeit übersteuert die Beschattung nach Sonnenstand.

Die Regel wird über eine Aktivierungsvariable aktiviert/deaktiviert.

Praktisches Beispielszenario:
Im Normalfall wird nach Sonnenstand beschattet. Wenn jedoch der Fernseher eingeschaltet wird, dann soll je nach Helligkeit stärker oder schwächer abgedunkelt werden.

### Erkennung von Kontakten (optional)
Um auf offene Fenster/Türen oder auch Regen/Sturm reagieren zu können, können bis zu vier Kontakte angegeben werden.
Je zwei Kontakte dienen dem Öffnen (Fenster/Tür) sowie dem Schließen (Regen/Wind) eines Rollladens.
Je Kontakt ist anzugeben, in welche Position mindestens gefahren werden soll.   
Wird ein Kontakt als offen erkannt, dann wird sofort auf die gewünschte Position gefahren. Nach dem Schließen des Kontaktes wird die dann gültige Höhe neu ermittelt und sofort angefahren.

Sonderfall: werden sowohl offene Kontakt zum Schließen als auch zum Öffnen des Rollladens erkannt (z.B. die Tür ist offen und es regnet), dann erhalten die Kontakte zum Öffnen Vorrang. 
 
### Blind Controller

| Eigenschaft | Typ     | Standardwert            | Funktion                                  |
| :--------- | :-----: | :------------------------| :--------------------------------------- |
| BlindLevelID               | integer | 0 | Statusvariable, des zu steuernden Rollladens. Sie muss vom Typ Integer oder Float sein und über ein korrektes Profil verfügen. |
| WeeklyTimeTableEventID     | integer | 0 | Verweis auf ein Wochenplanevent, dass die täglichen Grundzeiten für Rollladen rauf und Rollladen runter abbildet.       |                  |
| WakeUpTimeID               | integer | 0 | Indikatorvariable vom Typ String, die eine übersteuernde Hochfahrzeit beinhaltet. Die Zeit muss im Format 'HH:MM' angegeben sein|
| WakeUpTimeOffset               | integer | 0 | Offset zur WakeUpTime in Minuten|
| BedTimeID               | integer | 0 | Indikatorvariable vom Typ String, die eine übersteuernde Runterfahrzeit beinhaltet. Die Zeit muss im Format 'HH:MM' angegeben sein|
| BedTimeOffset               | integer | 0 | Offset zur BedTime in Minuten|
| HolidayIndicatorID         | integer | 0 | Indikatorvariable, die anzeigt, ob ein Urlaubs-/Feiertag anliegt|
| DayUsedWhenHoliday         | integer | 0 | legt fest, welcher Wochentag des Wochenplans im Fall eines Urlaubs-/Feiertages herangezogen werden soll|
| IsDayIndicatorID           | integer | 0 | Indikatorvariable, die anzeigt, ob es Tag oder Nacht ist. Es kann z.B. die ISDAY Statusvariable des Location Controls genutzt werden.
| BrightnessID               | integer | 0 | Indikatorvariable, die die Helligkeit zur Tag/Nacht Bestimmung abbildet.  |
| BrightnessAvgMinutes       | integer | 0 | Anzahl Minuten über die der Helligkeitsdurchschnitt gebildet werden soll  |
| BrightnessThresholdID      | integer | 0 | Indikatorvariable, die den Schwellwert zur Tag/Nacht Bestimmung zur Verfügung stellt |
| DayStartID               | integer | 0 | Indikatorvariable vom Typ String, die eine übersteuernde Tagesanfangszeit beinhaltet. Die Zeit muss im Format 'HH:MM' angegeben und kleiner als '12:00' sein|
| DayEndID               | integer | 0 | Indikatorvariable vom Typ String, die eine übersteuernde Tagesendezeit beinhaltet. Die Zeit muss im Format 'HH:MM' angegeben und größer als '12:00' sein|
| ContactOpen1ID, ContactOpen2ID       | integer | 0 | Indikatorvariablen: wenn eine der Variablen ungleich 0 ist, dann wird der Rollladen auf die unter 'ContactOpenLevel' angegebene Mindesthöhe gefahren
| ContactOpenLevel1, ContactOpenLevel2           | float   | 0 | Höhe, auf die der Rollladen mindestens gefahren wird, wenn der zugehörige Kontakt offen ist.
| EmergencyContactID        | integer   | 0 | Notfall Indikator: wenn der Kontakt ungleich 0 ist, wird der Rollladen sofort geöffnet. Gleichzeitig wird die Automatik außer Betrieb genommen.
| ActivatorIDShadowingBySunPosition | integer   | 0 | Indikatorvariable, die die Beschattungssteuerung nach Sonnenstand aktiviert. Wenn der Inhalt der zugewiesenen Variable >0 ist, dann ist die Steuerung aktiv
| AzimuthID| integer   | 0 | Indikatorvariable, die den aktuellen Sonnenstand (Richtung) wiedergibt.  
| AltitudeID      | integer   | 0 | Indikatorvariable, die den aktuellen Sonnenstand (Höhe) wiedergibt.
| AzimuthFrom      | integer   | 0 | Angabe, ab welcher Sonnenrichtung die Beschattungssteuerung aktiv sein soll
| AzimuthTo      | integer   | 0 | Angabe, bis zu welcher Sonnenrichtung die Beschattungssteuerung aktiv sein soll
| BrightnessIDShadowingBySunPosition      | integer   | 0 | Indikatorvariable, die die Helligkeit zur Beschattung nach Sonnenposition angibt
| BrightnessAvgMinutesShadowingBySunPosition      | integer   | 0 | Anzahl Minuten über die der Helligkeitsdurchschnitt gebildet werden soll 
| BrightnessThresholdIDShadowingBySunPosition      | integer   | 0 | Indikatorvariable, die den Schwellwert zur Beschattung nach Sonnenposition zur Verfügung stellt
| TemperatureIDShadowingBySunPosition      | integer   | 0 | Indikatorvariable die einen Temperatursensor (Außentemperatur) wiedergibt. 
| LowSunPositionAltitude<br>HighSunPositionAltitude<br>LowSunPositionBlindLevel<br>HighSunPositionBlindLevel | integer   | 0 | Aus diesen möglichst weit auseinanderliegenden Wertepaaren wird die Behanghöhe in Abhängigkeit von der Sonnenhöhe errechnet  
| ActivatorIDShadowingBrightness | integer   | 0 | Indikatorvariable, die die Beschattungssteuerung nach Helligkeit aktiviert. Wenn der Inhalt der zugewiesenen Variable >0 ist, dann ist die Steuerung aktiv
| BrightnessIDShadowingBrightness| integer   | 0 | Indikatorvariable, die die Helligkeit zur Beschattung nach Helligkeit angibt
| BrightnessAvgMinutesShadowingBrightness| integer   | 0 | Anzahl Minuten über die der Helligkeitsdurchschnitt bei der Beschattung nach Helligkeit gebildet werden soll
| ThresholdIDHighBrightness      | integer   | 0 | Indikatorvariable, die den hohen Helligkeitsschwellwert Zur Steuerung nach Helligkeit zur Verfügung stellt
| LevelHighBrightnessShadowingBrightness      | integer   | 0 | Level, der bei erreichen der hohen Helligkeit angefahren werden soll
| ThresholdIDLessBrightness      | integer   | 0 | Indikatorvariable, die den niedrigen Helligkeitsschwellwert Zur Steuerung nach Helligkeit zur Verfügung stellt
| LevelLessBrightnessShadowingBrightness      | integer   | 0 | Level, der bei erreichen der niedrigeren Helligkeit angefahren werden soll
| UpdateInterval             | integer | 1 | legt fest, in welchem Intervall die Steuerung durchgeführt wird |
| DeactivationAutomaticMovement | integer | 20| legt fest, wie lange nach einer automatischen Rollladenfahrt keine weitere automatische Fahrt mehr stattfinden soll. Das verhindert, dass z.B. bei Helligkeitsschwankungen der Rollladen in zu kleinen Intervallen bewegt wird. Die Zeit wird nicht berücksichtigt bei Kontakten und beim Tag/Nacht Wechsel.|
| DeactivationManualMovement | integer | 120  | legt fest, wie lange nach einer Rollladenfahrt, die nicht durch diese Steuerung veranlasst wurde (z.B. nach einer manuelle Betätigung) keine weitere automatische Fahrt mehr stattfinden soll. Die Zeit wird nicht berücksichtigt bei Kontakten und beim Tag/Nacht Wechsel.|
| WriteLogInformationToIPSLogger | boolean | false  | legt fest, ob die Log Informationen zusätzlich zum Standard Logfile auch an den IPSLogger der IPSLibrary übergeben werden sollen|
| WriteDebugInformationToIPSLogger | boolean | false  | legt fest, ob die Debug Informationen zusätzlich zum Debugger auch an den IPSLogger der IPSLibrary übergeben werden sollen|
| WriteDebugInformationToLogfile | boolean | false  | legt fest, ob die Debug Informationen zusätzlich in das Standard Logfile geschrieben werden sollen. Wichtig: dazu muss der Symcon Spezialschalter 'LogfileVerbose' aktiviert sein
 
## 6. Statusvariablen und Profile

Folgende Statusvariablen werden angelegt:

#####ACTIVATED
Über die Statusvariable kann die automatische Steuerung aktiviert und deaktiviert werden. Beim (Wieder-)Einschalten der automatischen Steuerung werden vorher erkannte manuelle Eingriffe verworfen.
 
#####LAST_MESSAGE
Die Statusvariable beinhaltet einen Hinweis über die letzte Bewegung. Um die Bewegungen eines Rollladens zu kontrollieren, bietet es sich an, die Archivierung für diese Variable einzuschalten. 
Dann werden im Webfront die Bewegungen in Form eines Logfiles dargestellt.  

## 7. Anhang

###  GUIDs und Datenaustausch

#### Blind Control (Modul)

GUID: `{7995E8C8-BD15-46A1-8AB6-2B795C33C0C5}` 

#### Blind Controller (Instanz)

GUID: `{538F6461-5410-4F4C-91D3-B39122152D56}` 

#### Blind Control Group Master (Instanz)

GUID: `{1ACD8A0D-5385-6D05-9537-F24C9014FD02}` 


