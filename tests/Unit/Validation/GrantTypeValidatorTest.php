<?php

namespace Cerberus\Tests\Unit\Validation;

use Cerberus\Validation\GrantType;
use Cerberus\Validation\GrantTypeValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class GrantTypeValidatorTest extends TestCase
{
    /**
     * @param string $grantType
     * @dataProvider validGrantTypeProvider
     */
    public function testItAllowsValidGrantTypes(string $grantType)
    {
        $validator = new GrantTypeValidator();
        /** @var ExecutionContextInterface|PHPUnit_Framework_MockObject_MockObject $context */
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $validator->initialize($context);

        $context->expects($this->never())
            ->method('addViolation');

        $validator->validate($grantType, new GrantType());
    }

    public function testItAddsViolationsForInvalidGrantType()
    {
        $validator = new GrantTypeValidator();
        $grantType = new GrantType();
        /** @var ExecutionContextInterface|PHPUnit_Framework_MockObject_MockObject $context */
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $validator->initialize($context);

        $context->expects($this->once())
            ->method('addViolation')
            ->with($grantType->message);

        $validator->validate("false_grant_type", $grantType);
    }

    public function validGrantTypeProvider(): array
    {
        return [
            ["password"],
            ["client_credentials"],
            ["implicit"],
            ["auth_code"],
            ["refresh_token"]
        ];
    }
}
