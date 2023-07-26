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

/* database/triggers/list.twig */
class __TwigTemplate_ee14ebdfc75d424f08e15e2a0f3b5e41 extends Template
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
        echo "<div class=\"container-fluid my-3\">
  <h2>
    ";
        // line 3
        echo PhpMyAdmin\Html\Generator::getIcon("b_triggers", _gettext("Triggers"));
        echo "
    ";
        // line 4
        echo PhpMyAdmin\Html\MySQLDocumentation::show("TRIGGERS");
        echo "
  </h2>

  <div class=\"d-flex flex-wrap my-3\">
    <div>
      <div class=\"input-group\">
        <div class=\"input-group-text\">
          <div class=\"form-check mb-0\">
            <input class=\"form-check-input checkall_box\" type=\"checkbox\" value=\"\" id=\"checkAllCheckbox\" form=\"rteListForm\">
            <label class=\"form-check-label\" for=\"checkAllCheckbox\">";
echo _gettext("Check all");
        // line 13
        echo "</label>
          </div>
        </div>
        <button class=\"btn btn-outline-secondary\" id=\"bulkActionExportButton\" type=\"submit\" name=\"submit_mult\" value=\"export\" form=\"rteListForm\" title=\"";
echo _gettext("Export");
        // line 16
        echo "\">
          ";
        // line 17
        echo PhpMyAdmin\Html\Generator::getIcon("b_export", _gettext("Export"));
        echo "
        </button>
        <button class=\"btn btn-outline-secondary\" id=\"bulkActionDropButton\" type=\"submit\" name=\"submit_mult\" value=\"drop\" form=\"rteListForm\" title=\"";
echo _gettext("Drop");
        // line 19
        echo "\">
          ";
        // line 20
        echo PhpMyAdmin\Html\Generator::getIcon("b_drop", _gettext("Drop"));
        echo "
        </button>
      </div>
    </div>

    <div class=\"ms-auto\">
      <a class=\"ajax add_anchor btn btn-primary";
        // line 26
        echo (( !($context["has_privilege"] ?? null)) ? (" disabled") : (""));
        echo "\" href=\"";
        echo PhpMyAdmin\Url::getFromRoute("/database/triggers", ["db" => ($context["db"] ?? null), "table" => ($context["table"] ?? null), "add_item" => true]);
        echo "\" role=\"button\"";
        echo (( !($context["has_privilege"] ?? null)) ? (" tabindex=\"-1\" aria-disabled=\"true\"") : (""));
        echo ">
        ";
        // line 27
        echo PhpMyAdmin\Html\Generator::getIcon("b_trigger_add", _gettext("Create new trigger"));
        echo "
      </a>
    </div>
  </div>

  <form id=\"rteListForm\" class=\"ajax\" action=\"";
        // line 32
        echo PhpMyAdmin\Url::getFromRoute((( !twig_test_empty(($context["table"] ?? null))) ? ("/table/triggers") : ("/database/triggers")));
        echo "\">
    ";
        // line 33
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), ($context["table"] ?? null));
        echo "

    <div id=\"nothing2display\"";
        // line 35
        echo (( !twig_test_empty(($context["items"] ?? null))) ? (" class=\"hide\"") : (""));
        echo ">
      ";
echo _gettext("There are no triggers to display.");
        // line 37
        echo "    </div>

    <table id=\"triggersTable\" class=\"table table-striped table-hover";
        // line 39
        echo ((twig_test_empty(($context["items"] ?? null))) ? (" hide") : (""));
        echo " w-auto data\">
      <thead>
        <tr>
          <th></th>
          <th>";
echo _gettext("Name");
        // line 43
        echo "</th>
          ";
        // line 44
        if (twig_test_empty(($context["table"] ?? null))) {
            // line 45
            echo "            <th>";
echo _gettext("Table");
            echo "</th>
          ";
        }
        // line 47
        echo "          <th>";
echo _gettext("Time");
        echo "</th>
          <th>";
echo _gettext("Event");
        // line 48
        echo "</th>
          <th colspan=\"3\"></th>
        </tr>
      </thead>
      <tbody>
        <tr class=\"hide\">";
        // line 53
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range(0, ((twig_test_empty(($context["table"] ?? null))) ? (7) : (6))));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            echo "<td></td>";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        echo "</tr>

        ";
        // line 55
        echo ($context["rows"] ?? null);
        echo "
      </tbody>
    </table>
  </form>
</div>
";
    }

    public function getTemplateName()
    {
        return "database/triggers/list.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  163 => 55,  151 => 53,  144 => 48,  138 => 47,  132 => 45,  130 => 44,  127 => 43,  119 => 39,  115 => 37,  110 => 35,  105 => 33,  101 => 32,  93 => 27,  85 => 26,  76 => 20,  73 => 19,  67 => 17,  64 => 16,  58 => 13,  45 => 4,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/triggers/list.twig", "/var/www/html/phpMyAdmin/templates/database/triggers/list.twig");
    }
}
