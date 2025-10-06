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

/* partials/sidebar.twig */
class __TwigTemplate_7a4861be71eb3203b36d48ac026760e7 extends Template
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
        yield "<style>
  .active {
    background-color: white  !important;
    color: black !important;
  }  }
</style>
<!--begin::Sidebar-->
<aside class=\"app-sidebar bg-body-secondary shadow\" data-bs-theme=\"dark\">
    <!--begin::Sidebar Brand-->
    <div class=\"sidebar-brand\">
        <a href=\"";
        // line 11
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/\" class=\"brand-link\">
            <img src=\"";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/images/dashboard-icon.png\" alt=\"AdminLTE Logo\" class=\"brand-image opacity-75 shadow\"/>
            <span class=\"brand-text fw-light\"> Dashboard</span>
        </a>
    </div>
    <!--end::Sidebar Brand-->
    
    <!--begin::Sidebar Wrapper-->
    <div class=\"sidebar-wrapper\">
        <nav class=\"mt-2\">
            <!--begin::Sidebar Menu-->
            <ul class=\"nav sidebar-menu flex-column\" data-lte-toggle=\"treeview\" role=\"navigation\" aria-label=\"Main navigation\" data-accordion=\"false\" id=\"navigation\">

                <!-- Administration -->
                <li class=\"nav-item ";
        // line 25
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['App\Views\ExtensionTwig']->isEnable(["/admin", "/admin/users", "/admin/users/create"], ($context["current_route"] ?? null)), "html", null, true);
        yield "\">
                    <a href=\"#\" class=\"nav-link ";
        // line 26
        yield (((($tmp = $this->extensions['App\Views\ExtensionTwig']->isEnable(["/admin/users", "/admin/users/create"], ($context["current_route"] ?? null))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("active") : (""));
        yield "\">
                        <i class=\"nav-icon bi bi-speedometer\"></i>
                        <p>
                            Administração
                            <i class=\"nav-arrow bi bi-chevron-right\"></i>
                        </p>
                    </a>
                    <ul class=\"nav nav-treeview\">
                        <!-- Usuário -->
                        <li class=\"nav-item ";
        // line 35
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['App\Views\ExtensionTwig']->isEnable(["/admin/users", "/admin/users/create"], ($context["current_route"] ?? null)), "html", null, true);
        yield "\">
                            <a href=\"#\" class=\"nav-link ";
        // line 36
        yield (((($tmp = $this->extensions['App\Views\ExtensionTwig']->isEnable(["/admin/users", "/admin/users/create"], ($context["current_route"] ?? null))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("active") : (""));
        yield "\">
                                <i class=\"nav-icon bi bi-circle\"></i>
                                <p>
                                    Usuário
                                    <i class=\"nav-arrow bi bi-chevron-right\"></i>
                                </p>
                            </a>
                            <ul class=\"nav nav-treeview\">
                                <li class=\"nav-item\">
                                    <a href=\"";
        // line 45
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/admin/users\" class=\"nav-link ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['App\Views\ExtensionTwig']->isActive("/admin/users", ($context["current_route"] ?? null)), "html", null, true);
        yield "\">
                                        <i class=\"nav-icon bi bi-circle\"></i>
                                        <p>Lista de usuários</p>
                                    </a>
                                </li>
                                <li class=\"nav-item\">
                                    <a href=\"";
        // line 51
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/admin/users/create\" class=\"nav-link ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['App\Views\ExtensionTwig']->isActive("/admin/users/create", ($context["current_route"] ?? null)), "html", null, true);
        yield "\">
                                        <i class=\"nav-icon bi bi-circle\"></i>
                                        <p>Cadastrar novo usuário</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- end Usuário -->
                    </ul>
                </li>
                <!-- end Administration -->
                
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "partials/sidebar.twig";
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
        return array (  117 => 51,  106 => 45,  94 => 36,  90 => 35,  78 => 26,  74 => 25,  58 => 12,  54 => 11,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "partials/sidebar.twig", "C:\\wamp64\\www\\dashboard\\templates\\pages\\partials\\sidebar.twig");
    }
}
