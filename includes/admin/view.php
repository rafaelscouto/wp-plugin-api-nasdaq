<?php
function my_load_assets() {
    wp_enqueue_script('jquery-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js', array(), false, true);
    wp_enqueue_script('axios-js', 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.25.0/axios.js', array('jquery-js'), null, true);
    wp_enqueue_script('main-js', plugins_url('assets/js/main.js', __FILE__), array('jquery-js'), false, true);
    wp_enqueue_style('main_css', plugins_url('assets/css/main.css', __FILE__ ), false, null);
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css');

    wp_localize_script('main-js', 'rc_api_nasdaq_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}

function rc_api_nasdaq_admin_page_html() {
    
    my_load_assets();

    ?>

    <div class="container">
        <div class="row">
            <h3 class="mt-2"><?php echo esc_html(get_admin_page_title()); ?></h3>
            <p>Configure o plugin para consumir a API Nasdaq</p>
            <div class="alert alert-warning" role="alert">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <h6>Atenção!</h6>
                        <p>Para algumas consultas é necessário ter a chave da API.</p>
                        <p>Para obter a <strong>chave da API</strong>, acesse o site <a class="alert-link" href="https://www.quandl.com/" target="_blank">Quandl</a> e crie uma conta.</p>
                        <p>Para obter o <strong>código da Database</strong>, acesse o site <a class="alert-link" href="https://www.quandl.com/data" target="_blank">Quandl</a> e escolha a base de dados desejada.</p>
                        <p>Para obter o <strong>código do Dataset (Empresa)</strong>, acesse o site <a class="alert-link" href="https://www.quandl.com/data" target="_blank">Quandl</a> e escolha a empresa desejada.</p>
                        <h6>Databases testadas:</h6>
                        <p>WIKI, XLON, CFTC, OWF, VOL, OPT, FRED e YC</p>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <h6>Use este shortcode para exibir os dados em qualquer lugar do site</h6>
                        <p><code>[rc_api_nasdaq database_code="WIKI" dataset_code="AAPL"]</code></p>
                        <h6>Paramentros Obrigatórios:</h6>
                        <p>database_code e dataset_code</p>
                        <h6>Paramentros Opcionais:</h6>
                        <p>api_key, start_date e end_date</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                <form id="form-api-nasdaq" method="post">
                    <div class="mb-3">
                        <label class="form-label" for="api_key">Chave da API</label>
                        <input type="text" class="form-control" id="api_key" name="api_key" value="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="database_code">Código Database</label>
                        <input type="text" required class="form-control" id="database_code" name="database_code" value="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="dataset_code">Código Empresa</label>
                        <input type="text" required class="form-control" id="dataset_code" name="dataset_code" value="">
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label" for="start_date">Data Inicial</label>
                                <input type="date" name="start_date" id="start_date">
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="end_date">Data Final</label>
                                <input type="date" name="end_date" id="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="text" id="btn-send-api-nasdaq" class="button button-primary">Enviar</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-12 col-12">
                <label class="form-label">Resultado:</label>
                <div id="resultAPI"></div>
            </div>
        </div>
    </div>
    
    <?php
}

