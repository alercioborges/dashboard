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

/* base.twig */
class __TwigTemplate_b14b79ec6b5f671f3b46943e05e74859 extends Template
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
        yield from $this->load("partials/head.twig", 1)->unwrap()->yield($context);
        // line 2
        yield "
";
        // line 3
        yield from $this->load("partials/header.twig", 3)->unwrap()->yield($context);
        // line 4
        yield "
";
        // line 5
        yield from $this->load("partials/sidebar.twig", 5)->unwrap()->yield($context);
        // line 6
        yield "
<!--begin::App Main-->
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
        // line 31
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 32
        yield "\t\t\t\t<!-- End Contant -->
\t\t\t</div>
\t\t\t<!--end::Container-->
\t\t</div>
\t\t<!--end::App Content-->
\t</main>
\t<!--end::App Main-->


";
        // line 41
        yield from $this->load("partials/footer.twig", 41)->unwrap()->yield($context);
        yield from [];
    }

    // line 31
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
        return "base.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  100 => 31,  95 => 41,  84 => 32,  82 => 31,  55 => 6,  53 => 5,  50 => 4,  48 => 3,  45 => 2,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% include 'partials/head.twig' %}

{% include 'partials/header.twig' %}

{% include 'partials/sidebar.twig' %}

<!--begin::App Main-->
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
\t<!--end::App Main-->


{% include 'partials/footer.twig' %}", "base.twig", "C:\\wamp64\\www\\dashboard\\templates\\pages\\base.twig");
    }
}
