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

/* users-create.twig */
class __TwigTemplate_2146a6f3b6101050106080d4f6b53599 extends Template
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
        yield "
\t<link href=\"";
        // line 5
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/css/form-validation.css\" rel=\"stylesheet\"/>

\t<div class=\"col-md-8 mx-auto border border-dark p-3 rounded\">
\t\t<form
\t\t\tid=\"userForm\" method=\"post\" action=\"";
        // line 9
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['App\Views\ExtensionTwig']->routeRedirect("users.store"), "html", null, true);
        yield "\">
\t\t\t<!-- firstname -->
\t\t\t<div class=\"form-group mb-3\">
\t\t\t\t<label for=\"firstname\" class=\"form-label\">Nome
\t\t\t\t\t<span class=\"text-danger\">*</span>
\t\t\t\t</label>
\t\t\t\t<input type=\"text\" value=\"";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((($_v0 = ($context["OLD_INPUT"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess ? ($_v0["firstname"] ?? null) : null), "html", null, true);
        yield "\" class=\"form-control border border-dark\" id=\"firstname\" name=\"firstname\" autofocus data-rules=\"required|min=2|max=30|onlyLetter\" placeholder=\"Digite seu nome\" onkeyup=\"this.value = this.value.toUpperCase()\" autocomplete=\"given-name\" aria-required=\"true\" spellcheck=\"false\">
\t\t\t\t";
        // line 16
        yield $this->extensions['App\Views\ExtensionTwig']->setMessage("firstname");
        yield "
\t\t\t</div>

\t\t\t<!-- lastname -->
\t\t\t<div class=\"form-group mb-3\">
\t\t\t\t<label for=\"lastname\" class=\"form-label\">Sobrenome
\t\t\t\t\t<span class=\"text-danger\">*</span>
\t\t\t\t</label>
\t\t\t\t<input type=\"text\" value=\"";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((($_v1 = ($context["OLD_INPUT"] ?? null)) && is_array($_v1) || $_v1 instanceof ArrayAccess ? ($_v1["lastname"] ?? null) : null), "html", null, true);
        yield "\" class=\"form-control border border-dark\" id=\"lastname\" name=\"lastname\" onkeyup=\"this.value = this.value.toUpperCase()\" data-rules=\"required|min=2|max=30|onlyLetter\" placeholder=\"Digite seu sobrenome\" autocomplete=\"lastname\" aria-required=\"true\" spellcheck=\"false\">
\t\t\t\t";
        // line 25
        yield $this->extensions['App\Views\ExtensionTwig']->setMessage("lastname");
        yield "
\t\t\t</div>

\t\t\t<!-- E-mail -->
\t\t\t<div class=\"form-group mb-3\">
\t\t\t\t<label for=\"email\" class=\"form-label\">E-mail
\t\t\t\t\t<span class=\"text-danger\">*</span>
\t\t\t\t</label>
\t\t\t\t<input type=\"email\" value=\"";
        // line 33
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((($_v2 = ($context["OLD_INPUT"] ?? null)) && is_array($_v2) || $_v2 instanceof ArrayAccess ? ($_v2["email"] ?? null) : null), "html", null, true);
        yield "\" class=\"form-control border border-dark\" id=\"email\" name=\"email\" data-rules=\"required|email\" autocomplete=\"email\" onkeyup=\"this.value = this.value.toLowerCase()\" aria-required=\"true\" inputmode=\"email\" spellcheck=\"false\">
\t\t\t\t";
        // line 34
        yield $this->extensions['App\Views\ExtensionTwig']->setMessage("email");
        yield "
\t\t\t</div>


\t\t\t<!-- User profile -->
\t\t\t<div class=\"form-group mb-3\">
\t\t\t\t<label for=\"role_id\" class=\"form-label\">Perfil de Usuário
\t\t\t\t\t<span class=\"text-danger\">*</span>
\t\t\t\t</label>
\t\t\t\t<select class=\"form-select border border-dark\" id=\"role_id\" name=\"role_id\" data-rules=\"required\">
\t\t\t\t\t<option value=\"\">Selecione um perfil</option>
\t\t\t\t\t<option value=\"1\" ";
        // line 45
        yield ((((($_v3 = ($context["OLD_INPUT"] ?? null)) && is_array($_v3) || $_v3 instanceof ArrayAccess ? ($_v3["role_id"] ?? null) : null) == "1")) ? ("selected") : (""));
        yield ">Administrador</option>
\t\t\t\t\t<option value=\"2\" ";
        // line 46
        yield ((((($_v4 = ($context["OLD_INPUT"] ?? null)) && is_array($_v4) || $_v4 instanceof ArrayAccess ? ($_v4["role_id"] ?? null) : null) == "2")) ? ("selected") : (""));
        yield ">Usuário</option>
\t\t\t\t\t<option value=\"3\" ";
        // line 47
        yield ((((($_v5 = ($context["OLD_INPUT"] ?? null)) && is_array($_v5) || $_v5 instanceof ArrayAccess ? ($_v5["role_id"] ?? null) : null) == "3")) ? ("selected") : (""));
        yield ">Moderador</option>
\t\t\t\t\t<option value=\"4\" ";
        // line 48
        yield ((((($_v6 = ($context["OLD_INPUT"] ?? null)) && is_array($_v6) || $_v6 instanceof ArrayAccess ? ($_v6["role_id"] ?? null) : null) == "4")) ? ("selected") : (""));
        yield ">Convidado</option>
\t\t\t\t</select>
\t\t\t\t";
        // line 50
        yield $this->extensions['App\Views\ExtensionTwig']->setMessage("role_id");
        yield "
\t\t\t</div>

\t\t\t<!-- Password -->
\t\t\t<div class=\"mb-3\">
\t\t\t\t<label for=\"senha\" class=\"form-label\">Senha
\t\t\t\t\t<span class=\"text-danger\">*</span>
\t\t\t\t</label>
\t\t\t\t<div class=\"password-group\">
\t\t\t\t\t<input type=\"password\" class=\"form-control border border-dark\" id=\"password\" name=\"password\" placeholder=\"Digite a senha\" data-rules=\"required|min=6|max=30\" aria-required=\"true\" spellcheck=\"false\">
\t\t\t\t\t<button type=\"button\" class=\"toggle-password\" id=\"togglePassword\" aria-label=\"Mostrar ou ocultar senha\">
\t\t\t\t\t\tMostrar senha
\t\t\t\t\t</button>
\t\t\t\t</div>
\t\t\t\t";
        // line 64
        yield $this->extensions['App\Views\ExtensionTwig']->setMessage("password");
        yield "
\t\t\t\t<div class=\"form-text\">A senha deve conter pelo menos 6 caracteres.</div>
\t\t\t</div>

\t\t\t<!-- Submit button -->
\t\t\t<div>
\t\t\t\t<button type=\"Submit\" class=\"btn btn-primary\">Cadastrar</button>
\t\t\t</div>
\t\t</form>
\t</div>

\t<!-- <script src=\"";
        // line 75
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/js/form-validation,js\"></script>-->

";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "users-create.twig";
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
        return array (  173 => 75,  159 => 64,  142 => 50,  137 => 48,  133 => 47,  129 => 46,  125 => 45,  111 => 34,  107 => 33,  96 => 25,  92 => 24,  81 => 16,  77 => 15,  68 => 9,  61 => 5,  58 => 4,  51 => 3,  40 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "users-create.twig", "C:\\wamp64\\www\\dashboard\\templates\\pages\\users-create.twig");
    }
}
