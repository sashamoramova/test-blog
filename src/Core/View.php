<?php

declare(strict_types=1);

namespace App\Core;

use Smarty;

class View
{
    private Smarty $smarty;

    public function __construct(string $templatesDir, string $compileDir, bool $debug = false)
    {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($templatesDir);
        $this->smarty->setCompileDir($compileDir);
        $this->smarty->escape_html = true;

        if ($debug) {
            $this->smarty->setForceCompile(true);
            $this->smarty->setCaching(false);
        }
    }

    public function assign(string $key, mixed $value): self
    {
        $this->smarty->assign($key, $value);
        return $this;
    }

    public function render(string $template): string
    {
        return $this->smarty->fetch($template);
    }

    public function display(string $template): void
    {
        $this->smarty->display($template);
    }
}
