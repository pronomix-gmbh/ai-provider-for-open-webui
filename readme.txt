=== AI Provider for Open WebUI ===
Contributors:      pronomix
Tags:              ai, open webui, openwebui, llm, api
Requires at least: 6.7
Tested up to:      6.9
Stable tag:        1.2.0
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Stellt Open WebUI als Provider für den WordPress AI Client bereit.

== Description ==

AI Provider for Open WebUI verbindet den WordPress AI Client mit deiner Open-WebUI-Instanz.

Das Plugin ergänzt einen eigenen Connector und eine übersichtliche Einstellungsseite unter **Einstellungen > Open WebUI**.

Funktionen im Überblick:

* Verbindung zu Open WebUI über deine Basis-URL und deinen API-Key
* Automatische Modell-Erkennung über `GET /api/models`
* Text-Generierung über `POST /api/chat/completions`
* Auswahl eines bevorzugten Modells für Text, Bild und Vision (sofern vom Modell unterstützt)
* Saubere Integration in die Connector-Verwaltung des AI-Plugins
* Übersetzbar nach WordPress-Standard (Text Domain: `ai-provider-for-open-webui`)

Hinweis:
Die Endpunkt-Erweiterung `/api` wird intern ergänzt. Trage nur die Open-WebUI-Basis-URL ein.

== Installation ==

1. Installiere und aktiviere das WordPress AI Plugin (AI Client).
2. Lade dieses Plugin nach `/wp-content/plugins/ai-provider-for-open-webui/` hoch.
3. Aktiviere **AI Provider for Open WebUI** in WordPress.
4. Öffne **Einstellungen > Open WebUI**.
5. Hinterlege dort:
* Open-WebUI-URL (z. B. `http://localhost:3000`)
* API-Key aus Open WebUI (`Settings > Account`)
* Optional: bevorzugtes Modell

== Screenshots ==

1. Open-WebUI-Connector im AI-Plugin mit Verbindungsstatus.
2. Open-WebUI-Einstellungsseite mit URL, API-Key und Modellauswahl.

== Frequently Asked Questions ==

= Welche URL soll ich eintragen? =

Die Basis-URL deiner Open-WebUI-Instanz, zum Beispiel:

* `http://localhost:3000` (lokal)
* `https://ai.deine-domain.tld` (gehostet)

Bitte kein `/api` anhängen.

= Wo finde ich den API-Key? =

In Open WebUI unter **Settings > Account**.

= Warum sehe ich die Meldung „The AI plugin requires a valid AI Connector …“? =

Diese Meldung erscheint, wenn im AI-Plugin kein gültiger Connector aktiv ist.
Prüfe:

* AI-Plugin ist aktiv
* Open-WebUI-URL ist erreichbar
* API-Key ist gültig
* Connector „Open WebUI“ ist verbunden

= Kann ich Umgebungsvariablen verwenden? =

Ja:

* `OPENWEBUI_BASE_URL`
* `OPENWEBUI_API_KEY`
* `OPENWEBUI_REQUEST_TIMEOUT`

== External Services ==

Dieses Plugin spricht mit einer externen Open-WebUI-API, sobald AI-Funktionen genutzt werden.

Service:

* Open WebUI API an der von dir konfigurierten URL

Übertragene Daten:

* Prompts und Kontextdaten der angeforderten AI-Funktion
* Modellname
* API-Key (als Authentifizierung gegenüber deiner Open-WebUI-Instanz)

Zeitpunkt der Übertragung:

* Nur bei aktiver Nutzung von AI-Funktionen (z. B. Generierung, Zusammenfassung, Alt-Text)

Rechtsgrundlage/Verantwortung:

* Die konkrete Datenverarbeitung hängt von deiner Open-WebUI-Instanz und deren Backend-Provider(n) ab.
* Du bist für die datenschutzkonforme Konfiguration deiner Instanz verantwortlich.

Projekt-/Branding-Informationen:

* https://docs.openwebui.com/brand/

== Branding and Rights ==

Dieses Plugin enthält das offizielle Open-WebUI-Logo zur Provider-Kennzeichnung.

Quelle und Nutzungshinweise:

* `third-party-notices.txt`

== Changelog ==

= 1.2.0 - 2026-03-26 =

* Konfiguration auf ein bevorzugtes Open-WebUI-Modell vereinfacht
* Fallback-Handling auf Basis von Modell-Fähigkeiten für Bild- und Alt-Text-Funktionen ergänzt
* Erkennung von Modell-Fähigkeiten verbessert, um nicht unterstützte Routen zu vermeiden

= 1.1.0 - 2026-03-26 =

* Bevorzugtes Modell mit manuellem Fallback-Feld und Modellvorschlägen ergänzt
* Standard-Timeout für Open-WebUI-Requests ergänzt (`OPENWEBUI_REQUEST_TIMEOUT`)
* Verhalten der Einstellungsseite und Connector-Synchronisierung verbessert

= 1.0.0 - 2026-03-26 =

* Erstveröffentlichung
* Open-WebUI-Provider für den WordPress AI Client registriert
* Einstellungsseite für URL und API-Key ergänzt
* Modell-Erkennung über die Open-WebUI-API implementiert
