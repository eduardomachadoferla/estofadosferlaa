<?php
$titulo = 'Sobre Nós - Ferla Estofados';
include __DIR__ . '/includes/header.php';
?>


<main class="about-page">

  <section class="about-hero">
    <div class="container">
      <h1>Sobre a Ferla Estofados</h1>
      <p>Conforto, elegância e qualidade em cada detalhe</p>
    </div>
  </section>

  <section class="about-content container">
    <div class="about-text">
      <h2>Nossa História</h2>
      <p>
        A Ferla Estofados nasceu com o propósito de transformar ambientes através do conforto e da sofisticação.
        Cada peça é desenvolvida com atenção aos detalhes, unindo design moderno e qualidade superior.
      </p>

      <p>
        Trabalhamos com materiais selecionados e acabamentos refinados, garantindo durabilidade e beleza em cada produto.
      </p>

      <h2>Nossa Missão</h2>
      <p>
        Proporcionar bem-estar e elegância para nossos clientes, oferecendo estofados que unem conforto,
        qualidade e estilo único.
      </p>

      <h2>Nossos Valores</h2>
      <ul>
        <li>✔ Qualidade em cada detalhe</li>
        <li>✔ Compromisso com o cliente</li>
        <li>✔ Design sofisticado</li>
        <li>✔ Conforto e durabilidade</li>
      </ul>

      <a href="<?= e(site_url('index.php')) ?>#produtos" class="btn">Ver Produtos</a>
    </div>
  </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>