#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <Servo.h>

// Pantalla LCD en I2C (0x27 o 0x3F)
LiquidCrystal_I2C lcd(0x27, 16, 2);
Servo servoCerradura;

// Entrada de señales desde el ESP8266
#define PIN_IN_PERMITIDO A0
#define PIN_IN_DENEGADO  A1

// Salidas hacia componentes
#define SERVO_PIN        9
#define LED_VERDE        2
#define LED_ROJO         3
#define BUZZER           4

const int ANGULO_CERRADO = 0;
const int ANGULO_ABIERTO = 90;

void setup() {
  pinMode(PIN_IN_PERMITIDO, INPUT);
  pinMode(PIN_IN_DENEGADO, INPUT);

  pinMode(LED_VERDE, OUTPUT);
  pinMode(LED_ROJO, OUTPUT);
  pinMode(BUZZER, OUTPUT);

  digitalWrite(LED_VERDE, LOW);
  digitalWrite(LED_ROJO, LOW);

  servoCerradura.attach(SERVO_PIN);
  servoCerradura.write(ANGULO_CERRADO);

  lcd.init();
  lcd.backlight();

  mostrarReposo();
}

void mostrarReposo() {
  digitalWrite(LED_VERDE, LOW);
  digitalWrite(LED_ROJO, LOW);

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(" SI LOCKER RFID ");
  lcd.setCursor(0, 1);
  lcd.print("Acerque Tarjeta ");
}

void pitidoExito() {
  digitalWrite(BUZZER, HIGH);
  delay(150);
  digitalWrite(BUZZER, LOW);
}

void pitidoError() {
  digitalWrite(BUZZER, HIGH);
  delay(100);
  digitalWrite(BUZZER, LOW);
  delay(100);
  digitalWrite(BUZZER, HIGH);
  delay(100);
  digitalWrite(BUZZER, LOW);
}

void loop() {
  // Cuando Laravel responde AUTORIZADO
  if (digitalRead(PIN_IN_PERMITIDO) == HIGH) {
    digitalWrite(LED_VERDE, HIGH);
    digitalWrite(LED_ROJO, LOW);

    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("ACCESO PERMITIDO");
    lcd.setCursor(0, 1);
    lcd.print("Abriendo locker.");

    pitidoExito();

    servoCerradura.write(ANGULO_ABIERTO);
    delay(3000); // Mantiene abierto 3 segundos

    servoCerradura.write(ANGULO_CERRADO);
    mostrarReposo();
  }

  // Cuando Laravel responde DENEGADO
  if (digitalRead(PIN_IN_DENEGADO) == HIGH) {
    digitalWrite(LED_VERDE, LOW);
    digitalWrite(LED_ROJO, HIGH);

    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("ACCESO DENEGADO ");
    lcd.setCursor(0, 1);
    lcd.print("Tarjeta Invalida");

    pitidoError();

    delay(2500);
    mostrarReposo();
  }

  delay(50);
}