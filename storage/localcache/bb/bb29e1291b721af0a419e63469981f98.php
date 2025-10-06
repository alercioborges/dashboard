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

/* users.twig */
class __TwigTemplate_9ad02b58c18bf2ae98840cdc3265a2de extends Template
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

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "base.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $this->parent = $this->load("base.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 4
        yield "\t";
        yield $this->extensions['App\Views\ExtensionTwig']->setMessage("message");
        yield "
\t";
        // line 5
        if ((($tmp =  !Twig\Extension\CoreExtension::testEmpty(($context["USERS"] ?? null))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield "\t\t
\t\t<div class=\"card-body table-responsive p-0\">
\t\t\t<table class=\"table table-striped table-hover\">
\t\t\t\t<thead>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th scope=\"col\">#</th>
\t\t\t\t\t\t<th scope=\"col\">Nome</th>
\t\t\t\t\t\t<th scope=\"col\">E-mail</th>
\t\t\t\t\t\t<th scope=\"col\">Perfil</th>
\t\t\t\t\t</tr>
\t\t\t\t</thead>
\t\t\t\t<tbody class=\"table-group-divider\">
\t\t\t\t\t";
            // line 17
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["USERS"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["data"]) {
                // line 18
                yield "\t\t\t\t\t\t<tr class=\"align-middle\">
\t\t\t\t\t\t\t<td>";
                // line 19
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "id", [], "any", false, false, false, 19), "html", null, true);
                yield "</td>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t<a href=\"";
                // line 21
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['App\Views\ExtensionTwig']->routeRedirect("users.profile", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["data"], "id", [], "any", false, false, false, 21)]), "html", null, true);
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "name", [], "any", false, false, false, 21), "html", null, true);
                yield "</a>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t\t<td>";
                // line 23
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "email", [], "any", false, false, false, 23), "html", null, true);
                yield "</td>
\t\t\t\t\t\t\t<td>";
                // line 24
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "role", [], "any", false, false, false, 24), "html", null, true);
                yield "</td>
\t\t\t\t\t\t\t<td>
\t\t\t\t\t\t\t\t<a href=\"";
                // line 26
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
                yield "/admin/users/";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "id", [], "any", false, false, false, 26), "html", null, true);
                yield "/edit\" class=\"btn btn-primary btn-xs m-1\">
\t\t\t\t\t\t\t\t\t<i class=\"fa fa-edit\"></i>
\t\t\t\t\t\t\t\tEditar</a>

\t\t\t\t\t\t\t\t<!-- Start Modal delete user -->
\t\t\t\t\t\t\t\t<!-- Start link delete user Modal -->
\t\t\t\t\t\t\t\t<a href=\"#\" data-toggle=\"modal\" data-target=\"#confirm-delete-";
                // line 32
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "id", [], "any", false, false, false, 32), "html", null, true);
                yield "\" class=\"btn btn-danger btn-xs m-1\">
\t\t\t\t\t\t\t\t\t<i class=\"fa fa-trash\"></i>
\t\t\t\t\t\t\t\tExcluir</a>
\t\t\t\t\t\t\t\t<!-- End link delete Modal -->
\t\t\t\t\t\t\t\t<!-- Start window Modal -->
\t\t\t\t\t\t\t\t<div class=\"modal fade\" id=\"confirm-delete-";
                // line 37
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "id", [], "any", false, false, false, 37), "html", null, true);
                yield "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">
\t\t\t\t\t\t\t\t\t<div class=\"modal-dialog modal-dialog-centered modal-lg\" role=\"document\">
\t\t\t\t\t\t\t\t\t\t<div class=\"modal-content\">
\t\t\t\t\t\t\t\t\t\t\t<div class=\"modal-header\">
\t\t\t\t\t\t\t\t\t\t\t\t<h3>
\t\t\t\t\t\t\t\t\t\t\t\t\t<b>Exclusão de usuário</b>
\t\t\t\t\t\t\t\t\t\t\t\t</h3>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t<div class=\"modal-body\">
\t\t\t\t\t\t\t\t\t\t\t\t<p>
\t\t\t\t\t\t\t\t\t\t\t\t\t<h6>Deseja realmente excluir o usuário de nome
\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
                // line 48
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "firstname", [], "any", false, false, false, 48), "html", null, true);
                yield "
\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
                // line 49
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "lastname", [], "any", false, false, false, 49), "html", null, true);
                yield "
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<br>e de e-mail
\t\t\t\t\t\t\t\t\t\t\t\t\t";
                // line 51
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "email", [], "any", false, false, false, 51), "html", null, true);
                yield "?</h6>
\t\t\t\t\t\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t<div class=\"modal-footer\">
\t\t\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-success\" data-dismiss=\"modal\">Cancelar</button>
\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"";
                // line 56
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
                yield "/admin/users/delete/";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["data"], "id", [], "any", false, false, false, 56), "html", null, true);
                yield "\" class=\"btn btn-danger btn-ok\">Deletar</a>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</td>
\t\t\t\t\t\t</tr>
\t\t\t\t\t</div>
\t\t\t\t\t<!-- End window Modal -->
\t\t\t\t\t<!-- End modal delete user -->
\t\t\t\t</tbody>
\t\t\t</td>
\t\t</tr>
\t</tr>
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['data'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 71
            yield "</tbody>
</table>
</div>
";
        } else {
            // line 75
            yield "\t<div class=\"alert alert-danger mt-1\" role=\"alert\"><strong>Nenhum usuário encontrado</strong></div>
";
        }
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "users.twig";
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
        return array (  185 => 75,  179 => 71,  156 => 56,  148 => 51,  143 => 49,  139 => 48,  125 => 37,  117 => 32,  106 => 26,  101 => 24,  97 => 23,  90 => 21,  85 => 19,  82 => 18,  78 => 17,  63 => 5,  58 => 4,  51 => 3,  40 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "users.twig", "C:\\wamp64\\www\\dashboard\\templates\\pages\\users.twig");
    }
}
