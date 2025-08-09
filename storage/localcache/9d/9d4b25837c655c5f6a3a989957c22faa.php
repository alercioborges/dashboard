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

/* partials/head.twig */
class __TwigTemplate_fb14b34e1835ba7e98c9edc6269a3131 extends Template
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
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!doctype html>
<html lang=\"pt-br\">
  <!--begin::Head-->
  <head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    <title>";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["TITLE"] ?? null), "html", null, true);
        yield "</title>
    <!--begin::Accessibility Meta Tags-->
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, user-scalable=yes\" />
    <meta name=\"color-scheme\" content=\"light dark\" />
    <meta name=\"theme-color\" content=\"#007bff\" media=\"(prefers-color-scheme: light)\" />
    <meta name=\"theme-color\" content=\"#1a1a1a\" media=\"(prefers-color-scheme: dark)\" />
    <!--end::Accessibility Meta Tags-->
    <!--begin::Primary Meta Tags-->
    <meta name=\"title\" content=\"";
        // line 14
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["TITLE"] ?? null), "html", null, true);
        yield "\" />
    <meta name=\"author\" content=\"ColorlibHQ\" />
    
    
    <!--end::Primary Meta Tags-->
    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name=\"supported-color-schemes\" content=\"light dark\" />
    <link rel=\"preload\" href=\"";
        // line 22
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/css/adminlte.css\" as=\"style\" />
    <!--end::Accessibility Features-->
    <!--begin::Fonts-->
    <link
      rel=\"stylesheet\"
      href=\"";
        // line 27
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/css/index.css\"
      media=\"print\"
      onload=\"this.media='all'\"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel=\"stylesheet\"
      href=\"";
        // line 35
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/css/overlayscrollbars.min.css\" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel=\"stylesheet\"
      href=\"https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css\"
      crossorigin=\"anonymous\"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel=\"stylesheet\" href=\"";
        // line 45
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/css/adminlte.css\" />
    <!--end::Required Plugin(AdminLTE)-->
    <!-- apexcharts -->
    <link
      rel=\"stylesheet\"
      href=\"";
        // line 50
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/css/apexcharts.css\"
    />
  </head>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "partials/head.twig";
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
        return array (  111 => 50,  103 => 45,  90 => 35,  79 => 27,  71 => 22,  60 => 14,  49 => 6,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!doctype html>
<html lang=\"pt-br\">
  <!--begin::Head-->
  <head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    <title>{{ TITLE }}</title>
    <!--begin::Accessibility Meta Tags-->
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, user-scalable=yes\" />
    <meta name=\"color-scheme\" content=\"light dark\" />
    <meta name=\"theme-color\" content=\"#007bff\" media=\"(prefers-color-scheme: light)\" />
    <meta name=\"theme-color\" content=\"#1a1a1a\" media=\"(prefers-color-scheme: dark)\" />
    <!--end::Accessibility Meta Tags-->
    <!--begin::Primary Meta Tags-->
    <meta name=\"title\" content=\"{{ TITLE }}\" />
    <meta name=\"author\" content=\"ColorlibHQ\" />
    
    
    <!--end::Primary Meta Tags-->
    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name=\"supported-color-schemes\" content=\"light dark\" />
    <link rel=\"preload\" href=\"{{ base_path }}/assets/adminlte/css/adminlte.css\" as=\"style\" />
    <!--end::Accessibility Features-->
    <!--begin::Fonts-->
    <link
      rel=\"stylesheet\"
      href=\"{{ base_path }}/assets/adminlte/css/index.css\"
      media=\"print\"
      onload=\"this.media='all'\"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel=\"stylesheet\"
      href=\"{{ base_path }}/assets/adminlte/css/overlayscrollbars.min.css\" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel=\"stylesheet\"
      href=\"https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css\"
      crossorigin=\"anonymous\"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel=\"stylesheet\" href=\"{{ base_path }}/assets/adminlte/css/adminlte.css\" />
    <!--end::Required Plugin(AdminLTE)-->
    <!-- apexcharts -->
    <link
      rel=\"stylesheet\"
      href=\"{{ base_path }}/assets/adminlte/css/apexcharts.css\"
    />
  </head>", "partials/head.twig", "C:\\wamp64\\www\\dashboard\\templates\\pages\\partials\\head.twig");
    }
}
