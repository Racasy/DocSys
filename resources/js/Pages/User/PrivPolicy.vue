<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Privātuma politika" />

  <AuthenticatedLayout>
    <div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="bg-white p-8 rounded-2xl shadow-md">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Privātuma politika</h1>

        <section class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">1. Vispārīga informācija</h2>
          <p class="text-gray-700">Šī privātuma politika izskaidro, kā SIA "Abrams Business Services" (turpmāk – "Mēs", "Mūsu", "Uzņēmums") apstrādā, glabā un aizsargā personas datus mūsu dokumentu pārvaldības sistēmas izmantošanas laikā.</p>
        </section>

        <section class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">2. Kādus datus mēs apstrādājam?</h2>
          <ul class="list-disc list-inside text-gray-700">
            <li>E-pasta adrese</li>
            <li>Tālruņa numurs</li>
            <li>Lietotāja darbību žurnāls (IP adrese, darbības laiks un veids)</li>
            <li>Augšupielādētie dokumenti un komentāri</li>
            <li>Paroles hash</li>
          </ul>
        </section>

        <section class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">3. Datu apstrādes mērķi</h2>
          <p class="text-gray-700">Mēs apstrādājam datus lietotāja identifikācijai, pakalpojumu sniegšanai, juridisko pienākumu izpildei un drošības nolūkiem. Apstrāde balstīta uz līguma izpildi, juridiskām prasībām un leģitīmu interesi.</p>
        </section>

        <section class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">4. Glabāšanas ilgums</h2>
          <p class="text-gray-700">Dokumenti, to komentāri un lietotāja darbību žurnāli tiek glabāti <strong>X gadus</strong> saskaņā ar Latvijas normatīvajiem aktiem.</p>
        </section>

        <section class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">5. Kur dati tiek glabāti?</h2>
          <p class="text-gray-700">Faili tiek apstrādāti iekšēji un glabāti <strong>Google Cloud Platform</strong>, Eiropas Savienībā. Paroles tiek droši hash’otas (piemēram, ar bcrypt).</p>
        </section>

        <section class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">6. Lietotāja tiesības</h2>
          <ul class="list-disc list-inside text-gray-700">
            <li>Piekļuve saviem datiem</li>
            <li>Datu labošana</li>
            <li>Datu dzēšana saskaņā ar likumu</li>
            <li>Datu pārnesamība</li>
            <li>Iebildumi pret apstrādi</li>
          </ul>
        </section>

        <section class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">7. Datu dzēšana</h2>
          <p class="text-gray-700">Lietotājs var pieprasīt savu datu dzēšanu. Ja saglabāšana nav obligāta saskaņā ar likumu, dati tiks dzēsti.</p>
        </section>

        <section class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">8. Drošība</h2>
          <p class="text-gray-700">Dati tiek aizsargāti ar šifrēšanu, autentifikāciju, piekļuves kontroles mehānismiem un darbību žurnālu uzraudzību.</p>
        </section>

        <section class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">9. Kontaktinformācija</h2>
          <p class="text-gray-700">
            SIA "Abrams Business Services"<br />
            E-pasts: info@abrams.lv<br />
            Juridiskā adrese: [ievietot]<br />
            Reģistrācijas Nr.: [ievietot]
          </p>
        </section>

        <section>
          <h2 class="text-xl font-semibold text-gray-800 mb-2">10. Sūdzību iesniegšana</h2>
          <p class="text-gray-700">Ja uzskatāt, ka Jūsu dati tiek apstrādāti neatbilstoši, varat sazināties ar mums vai ar Datu valsts inspekciju (<a href="https://www.dvi.gov.lv" class="text-blue-600 underline" target="_blank">www.dvi.gov.lv</a>).</p>
        </section>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
