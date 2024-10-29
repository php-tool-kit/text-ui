<?php
namespace TextUI\Tests\Output;

use PHPUnit\Framework\TestCase;
use TextUI\Output\Box;
use TextUI\Utils;

/**
 * Unit tests for TextUI\Output\Box class.
 *
 * @author everton3x
 */
class BoxTest extends TestCase
{

    public function testSingleLineDraw(): void
    {
        $content = 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...';
        $terminalObject = new Box($content);
        $expectedOutput = file_get_contents('tests/assets/box-single-line.output');
        
        $this->expectsOutput($expectedOutput);
        $terminalObject->draw();
    }

    public function testMultiLineDraw(): void
    {
        $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer id luctus lorem. Integer sed enim et eros mattis eleifend. Praesent id lorem porta, consequat lectus id, rutrum libero. Phasellus eu cursus libero. Aenean fringilla orci ligula, quis laoreet ex scelerisque at. Etiam lacinia, tellus in placerat hendrerit, dolor est pharetra nunc, eu pharetra dui augue in nulla. Aliquam erat volutpat. Morbi vitae gravida ex, sollicitudin ullamcorper quam.
Etiam fermentum enim quis elementum volutpat. Vestibulum vitae diam dui. Praesent finibus consequat nisl id imperdiet. Donec malesuada ligula at convallis mollis. Donec metus enim, laoreet at nisi at, fermentum vulputate erat. Fusce scelerisque eu lectus eu blandit. Proin a sollicitudin sem. Vestibulum congue ex id pulvinar egestas.
Nunc aliquam vehicula neque quis viverra. Curabitur sapien purus, ornare at diam ultrices, euismod ultricies diam. Quisque ut ipsum vestibulum, rutrum leo at, lobortis enim. Praesent sit amet augue at leo efficitur feugiat non sed lorem. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eget nibh dignissim, convallis odio id, pellentesque orci. Mauris in dolor et turpis ullamcorper hendrerit nec vitae lacus. Quisque eget ipsum turpis. Mauris vel urna massa. Nunc gravida augue vel nulla volutpat volutpat. Curabitur tempus felis auctor, dignissim velit a, tincidunt massa.
Sed maximus sapien enim, eu tincidunt quam vestibulum ac. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Mauris sollicitudin nibh ac hendrerit tempor. Morbi euismod congue dictum. Ut nec egestas magna, vitae vulputate magna. Mauris cursus tellus non neque venenatis fermentum. Cras vitae ultricies augue. Cras non risus et enim pretium pulvinar in vel nibh. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce tempor interdum ornare. Donec fermentum justo id sem auctor, vel luctus urna pretium. Vestibulum maximus vel enim sed dignissim. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
Morbi quis libero sit amet libero ultrices convallis. Ut lacinia, dui vitae porttitor pretium, ex tellus lobortis arcu, vel luctus urna elit ac sapien. Curabitur volutpat justo sit amet arcu dictum varius. Nam eget pulvinar mi. Ut iaculis lorem sit amet sem lobortis varius. Sed consequat rhoncus luctus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris pulvinar nibh sem, ac pretium magna tincidunt sed. Integer vitae diam sed erat tristique congue. Vivamus magna erat, venenatis vel vestibulum a, rutrum a sem. In sagittis tempor mauris, nec venenatis ex posuere a. Duis aliquet, orci ac consequat dictum, lectus diam hendrerit erat, nec condimentum tellus diam et mauris. Pellentesque tincidunt enim sit amet elit fermentum pellentesque.';
        $terminalObject = new Box($content);
        $expectedOutput = file_get_contents('tests/assets/box-multi-line.output');
        
        $this->expectsOutput($expectedOutput);
        $terminalObject->draw();
    }
}
