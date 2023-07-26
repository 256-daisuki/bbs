<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* recent_favorite_table_favorite.twig */
class __TwigTemplate_3ddd1560744ae5c28ba80b8d3894d972 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["tables"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["table"]) {
            // line 2
            echo "<li class=\"warp_link\">
  <a
    class=\"ajax favorite_table_anchor\"
    href=\"";
            // line 5
            echo PhpMyAdmin\Url::getFromRoute("/database/structure/favorite-table", twig_get_attribute($this->env, $this->source, $context["table"], "remove_parameters", [], "any", false, false, false, 5));
            echo "\"
    title=\"";
echo _gettext("Remove from Favorites");
            // line 6
            echo "\"
    data-favtargetn=\"";
            // line 7
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["table"], "table_parameters", [], "any", false, false, false, 7), "md5", [], "any", false, false, false, 7), "html", null, true);
            echo "\"
  >
    ";
            // line 9
            echo PhpMyAdmin\Html\Generator::getIcon("b_favorite");
            echo "
  </a>
  <a href=\"";
            // line 11
            echo PhpMyAdmin\Url::getFromRoute("/table/recent-favorite", twig_get_attribute($this->env, $this->source, $context["table"], "table_parameters", [], "any", false, false, false, 11));
            echo "\">
    `";
            // line 12
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["table"], "table_parameters", [], "any", false, false, false, 12), "db", [], "any", false, false, false, 12), "html", null, true);
            echo "`.`";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["table"], "table_parameters", [], "any", false, false, false, 12), "table", [], "any", false, false, false, 12), "html", null, true);
            echo "`
  </a>
</li>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['table'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "recent_favorite_table_favorite.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 12,  64 => 11,  59 => 9,  54 => 7,  51 => 6,  46 => 5,  41 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "recent_favorite_table_favorite.twig", "/var/www/html/phpMyAdmin/templates/recent_favorite_table_favorite.twig");
    }
}
