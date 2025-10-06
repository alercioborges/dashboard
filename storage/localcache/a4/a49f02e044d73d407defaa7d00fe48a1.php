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

/* user-edit.twig */
class __TwigTemplate_9b91ecca2eb194556765b1402ff990e9 extends Template
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
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['App\Views\ExtensionTwig']->routeRedirect("users.update", ["id" => (($_v0 = ($context["USER_DATA"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess ? ($_v0["id"] ?? null) : null)]), "html", null, true);
        yield "\">
\t\t\t<!-- firstname -->
\t\t\t<div class=\"form-group mb-3\">
\t\t\t\t<label for=\"firstname\" class=\"form-label\">Nome
\t\t\t\t\t<span class=\"text-danger\">*</span>
\t\t\t\t</label>
\t\t\t\t<input type=\"text\" value=\"";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["OLD_INPUT"] ?? null), "firstname", [], "array", true, true, false, 15)) ? (Twig\Extension\CoreExtension::default((($_v1 = ($context["OLD_INPUT"] ?? null)) && is_array($_v1) || $_v1 instanceof ArrayAccess ? ($_v1["firstname"] ?? null) : null), (($_v2 = ($context["USER_DATA"] ?? null)) && is_array($_v2) || $_v2 instanceof ArrayAccess ? ($_v2["firstname"] ?? null) : null))) : ((($_v3 = ($context["USER_DATA"] ?? null)) && is_array($_v3) || $_v3 instanceof ArrayAccess ? ($_v3["firstname"] ?? null) : null))), "html", null, true);
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
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["OLD_INPUT"] ?? null), "lastname", [], "array", true, true, false, 24)) ? (Twig\Extension\CoreExtension::default((($_v4 = ($context["OLD_INPUT"] ?? null)) && is_array($_v4) || $_v4 instanceof ArrayAccess ? ($_v4["lastname"] ?? null) : null), (($_v5 = ($context["USER_DATA"] ?? null)) && is_array($_v5) || $_v5 instanceof ArrayAccess ? ($_v5["lastname"] ?? null) : null))) : ((($_v6 = ($context["USER_DATA"] ?? null)) && is_array($_v6) || $_v6 instanceof ArrayAccess ? ($_v6["lastname"] ?? null) : null))), "html", null, true);
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
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["OLD_INPUT"] ?? null), "email", [], "array", true, true, false, 33)) ? (Twig\Extension\CoreExtension::default((($_v7 = ($context["OLD_INPUT"] ?? null)) && is_array($_v7) || $_v7 instanceof ArrayAccess ? ($_v7["email"] ?? null) : null), (($_v8 = ($context["USER_DATA"] ?? null)) && is_array($_v8) || $_v8 instanceof ArrayAccess ? ($_v8["email"] ?? null) : null))) : ((($_v9 = ($context["USER_DATA"] ?? null)) && is_array($_v9) || $_v9 instanceof ArrayAccess ? ($_v9["email"] ?? null) : null))), "html", null, true);
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
        yield ((((($_v10 = ($context["USER_DATA"] ?? null)) && is_array($_v10) || $_v10 instanceof ArrayAccess ? ($_v10["role_id"] ?? null) : null) == "1")) ? ("selected") : (""));
        yield ">Administrador</option>
\t\t\t\t\t<option value=\"2\" ";
        // line 46
        yield ((((($_v11 = ($context["USER_DATA"] ?? null)) && is_array($_v11) || $_v11 instanceof ArrayAccess ? ($_v11["role_id"] ?? null) : null) == "2")) ? ("selected") : (""));
        yield ">Usuário</option>
\t\t\t\t\t<option value=\"3\" ";
        // line 47
        yield ((((($_v12 = ($context["USER_DATA"] ?? null)) && is_array($_v12) || $_v12 instanceof ArrayAccess ? ($_v12["role_id"] ?? null) : null) == "3")) ? ("selected") : (""));
        yield ">Moderador</option>
\t\t\t\t\t<option value=\"4\" ";
        // line 48
        yield ((((($_v13 = ($context["USER_DATA"] ?? null)) && is_array($_v13) || $_v13 instanceof ArrayAccess ? ($_v13["role_id"] ?? null) : null) == "4")) ? ("selected") : (""));
        yield ">Convidado</option>
\t\t\t\t</select>
\t\t\t\t";
        // line 50
        yield $this->extensions['App\Views\ExtensionTwig']->setMessage("role_id");
        yield "
\t\t\t</div>

\t\t\t<!-- Submit button -->
\t\t\t<div>
\t\t\t\t<button type=\"Submit\" class=\"btn btn-primary\">Cadastrar</button>
\t\t\t</div>
\t\t</form>
\t</div>

\t<!-- <script src=\"";
        // line 60
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_path"] ?? null), "html", null, true);
        yield "/assets/js/form-validation,js\"></script> -->

";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "user-edit.twig";
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
        return array (  155 => 60,  142 => 50,  137 => 48,  133 => 47,  129 => 46,  125 => 45,  111 => 34,  107 => 33,  96 => 25,  92 => 24,  81 => 16,  77 => 15,  68 => 9,  61 => 5,  58 => 4,  51 => 3,  40 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "user-edit.twig", "C:\\wamp64\\www\\dashboard\\templates\\pages\\user-edit.twig");
    }
}
