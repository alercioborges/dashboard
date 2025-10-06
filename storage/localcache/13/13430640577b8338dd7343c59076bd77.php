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

/* partials/footer.twig */
class __TwigTemplate_e06f349b19bc4d8ec3cda1d9b8cdd669 extends Template
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
        yield "      <!--begin::Footer-->
      <footer class=\"app-footer\">
        <!--begin::To the end-->
        <div class=\"float-end d-none d-sm-inline\">Anything you want</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src=\"";
        // line 14
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/js/overlayscrollbars.browser.es6.min.js\">
      </script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src=\"";
        // line 19
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/js/popper.min.js\">
    </script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->
    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src=\"";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/js/bootstrap.min.js\">
    </script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src=\"";
        // line 27
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/js/adminlte.js\"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->
    <script src=\"";
        // line 30
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/js/scrollbar-theme.js\"></script>
    <!--end::OverlayScrollbars Configure-->
    <!-- OPTIONAL SCRIPTS -->
    <!-- apexcharts -->
    <script
      src=\"";
        // line 35
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/adminlte/js/apexcharts.min.js\">
    </script>
</html>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "partials/footer.twig";
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
        return array (  93 => 35,  85 => 30,  79 => 27,  73 => 24,  65 => 19,  57 => 14,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "partials/footer.twig", "C:\\wamp64\\www\\dashboard\\templates\\pages\\partials\\footer.twig");
    }
}
