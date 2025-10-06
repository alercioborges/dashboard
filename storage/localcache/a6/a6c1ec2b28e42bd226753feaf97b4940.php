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
class __TwigTemplate_18f81dd4c50806b0a2f60e1456b44a53 extends Template
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
<main
\tclass=\"app-main\">
\t<!--begin::App Content Header-->
\t<div
\t\tclass=\"app-content-header\">
\t\t<!--begin::Container-->
\t\t<div
\t\t\tclass=\"container-fluid\">
\t\t\t<!--begin::Row-->
\t\t\t<div class=\"row\">
\t\t\t\t<div class=\"col-sm-6\">
\t\t\t\t\t<h3 class=\"mb-0\">";
        // line 19
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["TITLE"] ?? null), "html", null, true);
        yield "</h3>
\t\t\t\t</div>
\t\t\t\t<div class=\"col-sm-6\">
\t\t\t\t\t<ol class=\"breadcrumb float-sm-efnd\">
\t\t\t\t\t\t<li class=\"breadcrumb-item\">
\t\t\t\t\t\t\t<a href=\"";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "\">Home</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t<li class=\"breadcrumb-item active\" aria-current=\"page\">";
        // line 26
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["TITLE"] ?? null), "html", null, true);
        yield "</li>
\t\t\t\t\t</ol>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<!--end::Row-->
\t\t</div>
\t\t<!--end::Container-->
\t</div>
\t<div
\t\tclass=\"app-content\">
\t\t<!--begin::Container-->
\t\t<div
\t\t\tclass=\"container-fluid\">
\t\t\t<!-- Start Contant -->
\t\t\t<div
\t\t\t\tclass=\"card mb-4\">
\t\t\t\t<!-- /.card-header -->
\t\t\t\t<div class=\"card-body responsive p-3\">
\t\t\t\t
\t\t\t\t";
        // line 45
        if (array_key_exists("ERROR", $context)) {
            // line 46
            yield "            \t\t<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">
                \t\t<i class=\"fas fa-exclamation-triangle\"></i> ";
            // line 47
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["ERROR"] ?? null), "html", null, true);
            yield "
                \t\t<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>
            \t\t</div>
        \t\t";
        } else {
            // line 51
            yield "\t\t\t\t\t";
            yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
            // line 52
            yield "\t\t\t\t";
        }
        // line 53
        yield "\t\t\t\t
\t\t\t\t\t</div>
\t\t\t\t\t<!-- /.card-body -->
\t\t\t\t</div>
\t\t\t\t<!-- /.card -->
\t\t\t\t<!-- End Contant -->
\t\t\t</div>
\t\t\t<!--end::Container-->
\t\t</div>
\t\t<!--end::App Content-->
\t</main>
\t<!--end::App Main-->
\t";
        // line 65
        yield from $this->load("partials/footer.twig", 65)->unwrap()->yield($context);
        yield from [];
    }

    // line 51
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
        return array (  142 => 51,  137 => 65,  123 => 53,  120 => 52,  117 => 51,  110 => 47,  107 => 46,  105 => 45,  83 => 26,  78 => 24,  70 => 19,  55 => 6,  53 => 5,  50 => 4,  48 => 3,  45 => 2,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "base.twig", "C:\\wamp64\\www\\dashboard\\templates\\pages\\base.twig");
    }
}
