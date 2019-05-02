<?php

namespace Specification\Akeneo\Pim\Enrichment\Component\Product\Factory\Value;

use Akeneo\Pim\Structure\Component\AttributeTypes;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use PhpSpec\ObjectBehavior;
use Akeneo\Pim\Enrichment\Component\Product\Exception\InvalidOptionsException;
use Akeneo\Pim\Enrichment\Component\Product\Factory\Value\OptionsValueFactory;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Pim\Structure\Component\Model\AttributeOptionInterface;
use Akeneo\Pim\Enrichment\Component\Product\Value\OptionsValue;
use Akeneo\Pim\Structure\Component\Repository\AttributeOptionRepositoryInterface;
use Prophecy\Argument;

class OptionsValueFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(OptionsValue::class, AttributeTypes::OPTION_MULTI_SELECT);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OptionsValueFactory::class);
    }

    function it_supports_multiselect_attribute_type()
    {
        $this->supports('foo')->shouldReturn(false);
        $this->supports(AttributeTypes::OPTION_MULTI_SELECT)->shouldReturn(true);
    }

    function it_creates_an_empty_multi_select_product_value(AttributeInterface $attribute) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('multi_select_attribute');
        $attribute->getType()->willReturn(AttributeTypes::OPTION_MULTI_SELECT);
        $attribute->getBackendType()->willReturn('options');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $productValue = $this->create(
            $attribute,
            null,
            null,
            []
        );

        $productValue->shouldReturnAnInstanceOf(OptionsValue::class);
        $productValue->shouldHaveAttribute('multi_select_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldBeEmpty();
    }

    function it_creates_a_localizable_and_scopable_empty_multi_select_product_value(AttributeInterface $attribute) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('multi_select_attribute');
        $attribute->getType()->willReturn(AttributeTypes::OPTION_MULTI_SELECT);
        $attribute->getBackendType()->willReturn('options');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $productValue = $this->create(
            $attribute,
            'ecommerce',
            'en_US',
            []
        );

        $productValue->shouldReturnAnInstanceOf(OptionsValue::class);
        $productValue->shouldHaveAttribute('multi_select_attribute');
        $productValue->shouldBeLocalizable();
        $productValue->shouldHaveLocale('en_US');
        $productValue->shouldBeScopable();
        $productValue->shouldHaveChannel('ecommerce');
        $productValue->shouldBeEmpty();
    }

    function it_creates_a_multi_select_product_value(AttributeInterface $attribute) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('multi_select_attribute');
        $attribute->getType()->willReturn(AttributeTypes::OPTION_MULTI_SELECT);
        $attribute->getBackendType()->willReturn('options');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $productValue = $this->create(
            $attribute,
            null,
            null,
            ['foo', 'bar']
        );

        $productValue->shouldReturnAnInstanceOf(OptionsValue::class);
        $productValue->shouldHaveAttribute('multi_select_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldHaveTheOptionCodes(['foo', 'bar']);
    }

    function it_sorts_options_in_a_multi_select_product_value(AttributeInterface $attribute) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('multi_select_attribute');
        $attribute->getType()->willReturn(AttributeTypes::OPTION_MULTI_SELECT);
        $attribute->getBackendType()->willReturn('options');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $productValue = $this->create(
            $attribute,
            null,
            null,
            ['foo', 'bar']
        );

        $productValue->shouldReturnAnInstanceOf(OptionsValue::class);
        $productValue->shouldHaveAttribute('multi_select_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldHaveTheOptionCodesSorted(['bar', 'foo']);
    }

    function it_creates_a_localizable_and_scopable_multi_select_product_value(AttributeInterface $attribute) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('multi_select_attribute');
        $attribute->getType()->willReturn(AttributeTypes::OPTION_MULTI_SELECT);
        $attribute->getBackendType()->willReturn('options');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $productValue = $this->create(
            $attribute,
            'ecommerce',
            'en_US',
            ['foo', 'bar']
        );

        $productValue->shouldReturnAnInstanceOf(OptionsValue::class);
        $productValue->shouldHaveAttribute('multi_select_attribute');
        $productValue->shouldBeLocalizable();
        $productValue->shouldHaveLocale('en_US');
        $productValue->shouldBeScopable();
        $productValue->shouldHaveChannel('ecommerce');
        $productValue->shouldHaveTheOptionCodes(['foo', 'bar']);
    }

    function it_throws_an_exception_if_provided_data_is_not_an_array(AttributeInterface $attribute) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('multi_select_attribute');
        $attribute->getType()->willReturn(AttributeTypes::OPTION_MULTI_SELECT);
        $attribute->getBackendType()->willReturn('options');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $exception = InvalidPropertyTypeException::arrayExpected(
            'multi_select_attribute',
            OptionsValueFactory::class,
            'foobar'
        );

        $this
            ->shouldThrow($exception)
            ->during('create', [$attribute, 'ecommerce', 'en_US', 'foobar']);
    }

    //TODO: WHAT TO DO WITH THIS KIND OF MAGIC ? I'VE PUT IT LIKE THAT TEMPORARY
    function it_creates_a_multi_select_product_value_even_if_provided_code_has_a_different_case(AttributeInterface $attribute) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('multi_select_attribute');
        $attribute->getType()->willReturn(AttributeTypes::OPTION_MULTI_SELECT);
        $attribute->getBackendType()->willReturn('options');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $productValue = $this->create(
            $attribute,
            null,
            null,
            ['foo', 'bar']
        );

        $productValue->shouldReturnAnInstanceOf(OptionsValue::class);
        $productValue->shouldHaveAttribute('multi_select_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldHaveTheOptionCodes(['foo', 'bar']);
    }

    function it_throws_an_exception_if_provided_data_is_not_an_array_of_strings(AttributeInterface $attribute
    ) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('multi_select_attribute');
        $attribute->getType()->willReturn(AttributeTypes::OPTION_MULTI_SELECT);
        $attribute->getBackendType()->willReturn('options');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $exception = InvalidPropertyTypeException::validArrayStructureExpected(
            'multi_select_attribute',
            'one of the options is not a string, "integer" given',
            OptionsValueFactory::class,
            [42]
        );

        $this
            ->shouldThrow($exception)
            ->during('create', [$attribute, 'ecommerce', 'en_US', [42]]);
    }

    public function getMatchers(): array
    {
        return [
            'haveAttribute'  => function ($subject, $attributeCode) {
                return $subject->getAttributeCode() === $attributeCode;
            },
            'beLocalizable'  => function ($subject) {
                return $subject->isLocalizable();
            },
            'haveLocale'     => function ($subject, $localeCode) {
                return $localeCode === $subject->getLocaleCode();
            },
            'beScopable'     => function ($subject) {
                return $subject->isScopable();
            },
            'haveChannel'    => function ($subject, $channelCode) {
                return $channelCode === $subject->getScopeCode();
            },
            'beEmpty'        => function ($subject) {
                return is_array($subject->getData()) && empty($subject->getData());
            },
            'haveTheOptionCodes' => function ($subject, $expectedOptionCodes) {
                return empty(array_diff($subject->getData(), $expectedOptionCodes))
                    && empty(array_diff($expectedOptionCodes, $subject->getData()));
            },
            'haveTheOptionCodesSorted' => function ($subject, $expectedOptionCodes) {
                $data = $subject->getData();
                if (count($data) !== count($expectedOptionCodes)) {
                    return false;
                }

                for ($i = 0; $i < count($expectedOptionCodes); $i++) {
                    if ($expectedOptionCodes[$i] !== $data[$i]) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }
}
