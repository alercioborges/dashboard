<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* partials/content.twig */
class __TwigTemplate_0a268dc179f95d288602e2746eddec3f extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!--begin::App Main-->
<main class=\"app-main\">
\t<!--begin::App Content Header-->
\t<div class=\"app-content-header\">
\t\t<!--begin::Container-->
\t\t<div class=\"container-fluid\">
\t\t\t<!--begin::Row-->
\t\t\t<div class=\"row\">
\t\t\t\t<div class=\"col-sm-6\"><h3 class=\"mb-0\">Dashboard v3</h3></div>
\t\t\t\t<div class=\"col-sm-6\">
\t\t\t\t\t<ol class=\"breadcrumb float-sm-end\">
\t\t\t\t\t\t<li class=\"breadcrumb-item\"><a href=\"#\">Home</a></li>
\t\t\t\t\t\t<li class=\"breadcrumb-item active\" aria-current=\"page\">Dashboard v3</li>
\t\t\t\t\t</ol>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<!--end::Row-->
\t\t</div>
\t\t<!--end::Container-->
\t</div>
\t<div class=\"app-content\">
\t\t<!--begin::Container-->
\t\t<div class=\"container-fluid\">
\t\t\t<!-- Start Contant -->
\t\t\t";
        // line 25
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 26
        yield "\t\t\t\t<!-- End Contant -->
\t\t\t</div>
\t\t\t<!--end::Container-->
\t\t</div>
\t\t<!--end::App Content-->
\t</main>
\t<!--end::App Main-->";
        yield from [];
    }

    // line 25
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "partials/content.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  82 => 25,  71 => 26,  69 => 25,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!--begin::App Main-->
<main class=\"app-main\">
\t<!--begin::App Content Header-->
\t<div class=\"app-content-header\">
\t\t<!--begin::Container-->
\t\t<div class=\"container-fluid\">
\t\t\t<!--begin::Row-->
\t\t\t<div class=\"row\">
\t\t\t\t<div class=\"col-sm-6\"><h3 class=\"mb-0\">Dashboard v3</h3></div>
\t\t\t\t<div class=\"col-sm-6\">
\t\t\t\t\t<ol class=\"breadcrumb float-sm-end\">
\t\t\t\t\t\t<li class=\"breadcrumb-item\"><a href=\"#\">Home</a></li>
\t\t\t\t\t\t<li class=\"breadcrumb-item active\" aria-current=\"page\">Dashboard v3</li>
\t\t\t\t\t</ol>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<!--end::Row-->
\t\t</div>
\t\t<!--end::Container-->
\t</div>
\t<div class=\"app-content\">
\t\t<!--begin::Container-->
\t\t<div class=\"container-fluid\">
\t\t\t<!-- Start Contant -->
\t\t\t{% block content %}{% endblock %}
\t\t\t\t<!-- End Contant -->
\t\t\t</div>
\t\t\t<!--end::Container-->
\t\t</div>
\t\t<!--end::App Content-->
\t</main>
\t<!--end::App Main-->", "partials/content.twig", "C:\\wamp64\\www\\dashboard\\templates\\pages\\partials\\content.twig");
    }
}
