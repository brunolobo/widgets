# Widgets para PactoCRM

*Pacote de Widgets originalmente criado pelo Arrilot e adaptado por Brunolobo para uso no PactoCRM*

## Instalação

1) ```composer require brunolobo/widgets```

Para Laravel >= 5.5, apenas isso.
Para Laravel < 5.5, continue lendo.

2) Registrar o service provider no `app.php`

```php
<?php

'providers' => [
    ...
    Brunolobo\Widgets\ServiceProvider::class,
],
?>
```

3) Adicionar os facades também.

```php
<?php

'aliases' => [
    ...
    'Widget'       => Brunolobo\Widgets\Facade::class,
    'AsyncWidget'  => Brunolobo\Widgets\AsyncFacade::class,
],
?>
```

## Uso

Considere que vamos criar um widget para exibir as últimas notícias na página.

Primeiro de tudo é criar um novo Widget com o comando artisan.
```bash
php artisan make:widget RecentNews
```
Este comando gera 2 arquivos:

1) `resources/views/widgets/recent_news.blade.php` é uma view vazia.

Adicione a opção "--plain" se você não precisa de uma view.

2) `app/Widgets/RecentNews` é uma classe de Widget.

```php
<?php

namespace App\Widgets;

use Brunolobo\Widgets\AbstractWidget;

class RecentNews extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        //

        return view('widgets.recent_news', [
            'config' => $this->config,
        ]);
    }
}
```

> Nota: Você pode usar seus próprios .stubs se precisar. Publique os arquivos de configuração para mudar os paths.

O último passo é chamar o widget.
Você tem algumas formas de fazer isso.

```php
@widget('recentNews')
```
ou
```php
{{ Widget::run('recentNews') }}
```
ou então
```php
{{ Widget::recentNews() }}
```

Não existe diferença entre os comandos, use conforme a sua preferência.

## Passando variaveis para o widget

### Pelo array de configuração

Imagine que vamos mostrar 5 notícias no widget de notícias, mas em alguns lugares exibiremos 10.
Isso é facilmente conseguido com o uso da variável $config:

```php
class RecentNews extends AbstractWidget
{
    ...
    protected $config = [
        'count' => 5
    ];
    ...
}

...
@widget('recentNews') // shows 5
@widget('recentNews', ['count' => 10]) // shows 10
```
`['count' => 10]` é um array de configuração que é acessado por $this->config.

O array ed configuração está disponível em todo widget.

> Nota: Campos do array de configuração não especificados na criação do widget não serão atualizados:

```php
class RecentNews extends AbstractWidget
{
    ...
    protected $config = [
        'count' => 5,
        'foo'   => 'bar'
    ];
    
    ...
}

@widget('recentNews', ['count' => 10]) // $this->config['foo'] continua sendo 'bar'
```

> Nota 2: Você pode querer (mas provavelmente não deve) criar seu próprio BaseWidget e herdar a partir dele.
Tudo bem. A única barreira neste caso é merger as configurações padrão entre pai e filho.
Neste caso faça o seguinte:

1) Não adicione a linha `protected $config = [...]` no filho.

2) Adicione conforme abaixo:

```php
public function __construct(array $config = [])
{
    $this->addConfigDefaults([
        'child_key' => 'bar'
    ]);

    parent::__construct($config);
}
```

### Diretamente

Você pode passar parâmetros diretamente para o metodo `run()`.

```php
@widget('recentNews', ['count' => 10], 'date', 'asc')
...
public function run($sortBy, $sortOrder) { }
...
```

O método `run()` é resolvido via Service Container, então a injeção no método está disponível.

## Namespaces

Por padrão o pacote tenta encontrar seu widget no namespace ```App\Widgets```.

Você pode alterar isso publicando a configuração do pacote (```php artisan vendor:publish --provider="Brunolobo\Widgets\ServiceProvider"```) e setando a propriedade `default_namespace`.

Ainda que usar o namespace padrão seja muito conveniente, em alguns casos você pode desejar mais flexibilidade. 
Por exemplo, se você tem dezenas de widgets faz sentido telos em diferentes 'pastas namespaced'.

Sem problema, você tem várias formas de chamar esses widgets:

1) Passar o nome completo a partir do `default_namespace` (basically `App\Widgets`) para o método `run()`.
```php
@widget('News\RecentNews', $config)
```

2) Usar notação de ponto.
```php
@widget('news.recentNews', $config)
```

3) FQCN também é uma opção.
```php
@widget('\App\Http\Some\Namespace\Widget', $config)
```

## Widgets assíncronos

Em alguns casos é necessário carregar o widget com AJAX.

Isso é conseguido de forma extremamente simples!
O que você precisa é mudar o facade ou diretiva blade - `Widget::` => `AsyncWidget::`, `@widget` => `@asyncWidget`.

Os parâmetros do widget são encriptados e enviados por ajax por debaixo dos panos. Então espere que os dados sejam encodados com `json_encoded()` e `json_decoded()` para desencodar.

> Nota: Você pode desligar a encriptação para um determinado widget setando a variável `public $encryptParams = false;` nele. No entanto esta ação deixa os parâmetros do widget publicamente acessíveis, então tenha certeza de não deixar nenhum ponto de vulnerabilidade.
Por exemplo, se você passar o user_id como parâmetro com a encriptação desligada, é interessante acrescentar outra variável de controle dentro do widget.

> Nota: Você pode setar `use_jquery_for_ajax_calls` para `true` no arquivo de configuração para usar chamadas ajax caso queira.

Por padrão, nada é exibido até que a chamada ajax tenha finalizado.

Isto pode ser customizado com a adição do método `placeholder()` na classe do widget.

```php
public function placeholder()
{
    return 'Carregando...';
}
```

> Nota: Se você precisa fazer alguma coisa com o pacote de rotas para carregar assincronamente o widget (se você roda em uma subpasta http://site.com/app/) você precisa copiar Brunolobo\Widgets\ServiceProvider para a pasta app, modificar de acordo com suas necessidades e registrar no Laravel.

## Widgets recarregáveis

Você pode ir além e atualizar o widget automaticamente a cada N segundos.

Basta setar a propriedade `$reloadTimeout` do widget e está feito.

```php
class RecentNews extends AbstractWidget
{
    /**
     * The number of seconds before each reload.
     *
     * @var int|float
     */
    public $reloadTimeout = 10;
}
```

Tanto widgets sync quanto async tornam-se recarregáveis.

Você deve usar essa função com cuidado, pois pode facilmente sobrecarregar seu aplicativo caso as chamadas ajax tenham tempo muito curto.
Considere usar web sockets também mas eles são mais difíceis de configurar.

## Container

Os widgets necessitam de alguma interação DOM então todos concentram suas saídas em um containet html.
Este container é definido pelo método `AbstractWidget::container()` e também pode ser customizado.

```php
/**
 * Async and reloadable widgets are wrapped in container.
 * You can customize it by overriding this method.
 *
 * @return array
 */
public function container()
{
    return [
        'element'       => 'div',
        'attributes'    => 'style="display:inline" class="brunolobo-widget-container"',
    ];
}
```

> Nota: Não são suportados widgets em cascata.

## Cache

Também existe uma forma simples de fazer o cache do widget.
Basta setar a propriedade $cacheTime no widget e pronto.

```php
class RecentNews extends AbstractWidget
{
    /**
     * The number of minutes before cache expires.
     * False means no caching at all.
     *
     * @var int|float|bool
     */
    public $cacheTime = 60;
}
```

O cache é desligado por padrão.
Uma cache key é criada pelo widget para controle. Você pode sobrescrever o método ```cacheKey``` se julgar necessário.

## Widget groups (extra)

Em alguns casos o Blade é perfeito para setar a posição e ordem dos widgets.
No entanto, algumas vezes você pode ter um comportamento diferente:

```php
// add several widgets to the 'sidebar' group anywhere you want (even in controller)
Widget::group('sidebar')->position(5)->addWidget('widgetName1', $config1);
Widget::group('sidebar')->position(4)->addAsyncWidget('widgetName2', $config2);

// display them in a view in the correct order
@widgetGroup('sidebar')
// or 
{{ Widget::group('sidebar')->display() }}
```

`position()` pode ser omitido.

`Widget::group('sidebar')->addWidget('files');` 

é igual a 

`Widget::group('sidebar')->position(100)->addWidget('files');`

Você pode configurar um separador que irá aparecer entre os widgets de um grupo.
`Widget::group('sidebar')->setSeparator('<hr>')->...;`

Você pode encapsular cada widget de um grupo usando o método `wrap` method como abaixo:
```php
Widget::group('sidebar')->wrap(function ($content, $index, $total) {
    // $total is a total number of widgets in a group.
    return "<div class='widget-{$index}'>{$content}</div>";
})->...;
```

### Removendo widgets de um grupo

Existem algumas formas de remover um ou mais widgets de um grupo depois que eles já estiverem adicionados.

1) Remover um widget pelo unique `id`
```php
$id1 = Widget::group('sidebar')->addWidget('files');
$id2 = Widget::group('sidebar')->addAsyncWidget('files');
Widget::group('sidebar')->removeById($id1); // Agora só o segundo wodget está no grupo
```

2) Remover todos os widgets com nome específico.
```php
Widget::group('sidebar')->addWidget('files');
Widget::group('sidebar')->addAsyncWidget('files');
Widget::group('sidebar')->removeByName('files'); // Widget group está vazio
```

3) Remover todos os widgets de uma posição específica.
```php
Widget::group('sidebar')->position(42)->addWidget('files');
Widget::group('sidebar')->position(42)->addAsyncWidget('files');
Widget::group('sidebar')->removeByPosition(42); // Widget group está vazio
```

4) Remover todos os widgets de uma vez.
```php
Widget::group('sidebar')->addWidget('files');
Widget::group('sidebar')->addAsyncWidget('files');
Widget::group('sidebar')->removeAll(); // Widget group está vazio
```

### Checando o estado de um grupo

`Widget::group('sidebar')->isEmpty(); // bool`

`Widget::group('sidebar')->any(); // bool`

`Widget::group('sidebar')->count(); // int`
