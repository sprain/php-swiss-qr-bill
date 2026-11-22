# Fonts for the tests of TcLibPdfOutput

tc-lib-pdf does not ship any fonts. The files in this directory are the font definitions of the core fonts
Helvetica, Helvetica-Bold and ZapfDingbats, as needed to run the tests. They contain font metrics only, no glyphs.

They were created with `Com\Tecnick\Pdf\Font\Import` of [tecnickcom/tc-lib-pdf-font](https://github.com/tecnickcom/tc-lib-pdf-font)
from the AFM files in [tecnickcom/tc-font-core14-afms](https://github.com/tecnickcom/tc-font-core14-afms),
which are distributed under the following license:

> This file and the 14 PostScript(R) AFM files it accompanies may be used, copied, and distributed for any purpose
> and without charge, with or without modification, provided that all copyright notices are retained; that the AFM
> files are not distributed without this file; that all modifications to this file or any of the AFM files are
> prominently noted in the modified file(s); and that this paragraph is not modified. Adobe Systems has no
> responsibility or obligation to support the use of the AFM files.
